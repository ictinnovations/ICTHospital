<?php
/**
 * ICTHospital - scheduled notification jobs.
 *
 * Replaces the school fee notification inherited from ICTSchool, which walked the
 * Student table and chased unpaid monthly school bills through stdBill/billHistory.
 * The hospital equivalent is an outstanding patient payment: a row in `payment`
 * where the amount received is short of the gross total.
 *
 * Every lookup is guarded. A missing table, a missing notification type or an
 * unconfigured ICTCore integration returns a described "skipped" result rather
 * than a 500, because this runs unattended from cron.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Ictcore_fees;
use App\Models\Ictcore_integration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CronjobController extends BaseController
{
    /** Contacts pushed to ICTCore in a single run. */
    protected $batchLimit = 200;

    /** Local numbers starting with a single 0 get this country code instead. */
    protected $countryCode = '92';

    /**
     * Remind patients who still owe money on a payment record.
     *
     * Returns a JSON summary so both the HTTP route and the console command can
     * report what happened.
     */
    public function paymentReminder()
    {
        $result = $this->runPaymentReminder();

        Log::info('ICTHospital payment reminder', $result);

        return response()->json($result);
    }

    /**
     * Kept so existing cron entries pointing at the old school route keep working.
     */
    public function feenotification()
    {
        return $this->paymentReminder();
    }

    /**
     * Does the work and returns a plain array. No output, no exits.
     */
    public function runPaymentReminder()
    {
        $skip = function ($reason) {
            return ['status' => 'skipped', 'reason' => $reason, 'contacts' => 0];
        };

        foreach (['payment', 'notification_type', 'ictcore_integration'] as $table) {
            if (! Schema::hasTable($table)) {
                return $skip('table ' . $table . ' is missing');
            }
        }

        $notification = DB::table('notification_type')
            ->whereIn('notification', ['payment', 'fess'])
            ->first();

        if (! $notification || empty($notification->type)) {
            return $skip('no notification type is configured for patient payments');
        }

        $integration = Ictcore_integration::where('type', $notification->type)->first();

        if (! $integration) {
            return $skip('no ICTCore integration is configured for type ' . $notification->type);
        }

        $outstanding = $this->outstandingPayments();

        if ($outstanding->isEmpty()) {
            return ['status' => 'ok', 'reason' => 'nothing outstanding', 'contacts' => 0];
        }

        $ict = new ICTCoreController();
        $telenor = isset($integration->method) && $integration->method === 'telenor';

        $groupId = $telenor
            ? $ict->telenor_apis('group', '', '', '', '', '')
            : $this->createIctcoreGroup($ict, $integration);

        if (! $groupId) {
            return $skip('ICTCore did not return a contact group, check the integration settings');
        }

        $numbers = [];

        foreach ($outstanding as $row) {
            $phone = $this->normalisePhone($row->phone);

            if ($phone === null) {
                continue;
            }

            if ($telenor) {
                $numbers[] = $phone;
                continue;
            }

            $contactId = $ict->ictcore_api('contacts', 'POST', [
                'first_name' => $row->name,
                'last_name' => '',
                'phone' => $phone,
                'email' => $row->email ?: '',
            ]);

            if ($contactId) {
                $ict->ictcore_api('contacts/' . $contactId . '/link/' . $groupId, 'PUT', []);
                $numbers[] = $phone;
            }
        }

        if (empty($numbers)) {
            return $skip('no usable phone numbers on the outstanding payments');
        }

        if ($telenor) {
            $ict->telenor_apis('add_contact', $groupId, implode(',', $numbers), '', '', '');
        }

        $campaign = $this->sendCampaign($ict, $integration, $notification, $groupId, $telenor);

        return [
            'status' => 'ok',
            'reason' => 'reminder campaign created',
            'contacts' => count($numbers),
            'campaign' => $campaign,
        ];
    }

    /**
     * Payments where the patient still owes something.
     *
     * `gross_total` and `amount_received` are varchar in the legacy schema, so both
     * are cast before they are compared.
     */
    protected function outstandingPayments()
    {
        $money = function ($column) {
            return DB::raw('CAST(COALESCE(NULLIF(' . $column . ', ""), "0") AS DECIMAL(15,2))');
        };

        $query = DB::table('payment')
            ->select([
                DB::raw('COALESCE(NULLIF(payment.patient_name, ""), patient.name) as name'),
                DB::raw('COALESCE(NULLIF(payment.patient_phone, ""), patient.phone) as phone'),
                DB::raw('patient.email as email'),
            ])
            ->leftJoin('patient', 'patient.id', '=', 'payment.patient')
            ->whereRaw(
                'CAST(COALESCE(NULLIF(payment.gross_total, ""), "0") AS DECIMAL(15,2))'
                . ' > CAST(COALESCE(NULLIF(payment.amount_received, ""), "0") AS DECIMAL(15,2))'
            )
            ->limit($this->batchLimit);

        unset($money);

        if (Schema::hasColumn('payment', 'status')) {
            $query->where(function ($q) {
                $q->whereNull('payment.status')
                  ->orWhereNotIn('payment.status', ['paid', 'Paid', 'cancelled', 'Cancelled']);
            });
        }

        return $query->get();
    }

    /**
     * Ask ICTCore for a contact group to hold this run's recipients.
     */
    protected function createIctcoreGroup(ICTCoreController $ict, $integration)
    {
        $configured = ! empty($integration->ictcore_url)
            && ! empty($integration->ictcore_user)
            && ! empty($integration->ictcore_password);

        if (! $configured) {
            return null;
        }

        return $ict->ictcore_api('groups', 'POST', [
            'name' => 'Patient payment reminder',
            'description' => 'Outstanding patient payments, created by the ICTHospital cron job',
        ]);
    }

    /**
     * Build the message and hand the group to ICTCore or Telenor.
     */
    protected function sendCampaign(ICTCoreController $ict, $integration, $notification, $groupId, $telenor)
    {
        $message = $this->reminderMessage();

        if ($telenor) {
            $settings = Ictcore_fees::first();

            return $ict->telenor_apis(
                'campaign_create',
                $groupId,
                '',
                $message,
                $settings ? $settings->telenor_file_id : '',
                $notification->type
            );
        }

        $settings = Ictcore_fees::first();
        $programId = $settings ? $settings->ictcore_program_id : null;

        if ($notification->type === 'sms' || empty($programId)) {
            $textId = $ict->ictcore_api('messages/texts', 'POST', [
                'name' => 'payment_reminder',
                'data' => $message,
                'type' => 'utf-8',
                'description' => '',
            ]);

            $programId = $ict->ictcore_api('programs/sendsms', 'POST', [
                'name' => 'payment_reminder',
                'text_id' => $textId,
            ]);
        }

        if (empty($programId)) {
            return null;
        }

        return $ict->ictcore_api('campaigns', 'POST', [
            'program_id' => $programId,
            'group_id' => $groupId,
            'delay' => '',
            'try_allowed' => '',
            'account_id' => isset($integration->ictcore_account_id) ? $integration->ictcore_account_id : 1,
        ]);
    }

    /**
     * Operator supplied text, or a sane default.
     */
    protected function reminderMessage()
    {
        $settings = Ictcore_fees::first();

        if ($settings && ! empty($settings->description)) {
            return $settings->description;
        }

        return 'You have an outstanding balance at our hospital. Please contact reception to settle it.';
    }

    /**
     * Turn a stored number into something the gateway will accept, or null.
     */
    protected function normalisePhone($phone)
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return null;
        }

        if (preg_match('~^0\d+$~', $phone)) {
            $phone = $this->countryCode . substr($phone, 1);
        }

        $digits = preg_replace('/\D/', '', $phone);

        return strlen($digits) >= 10 ? $digits : null;
    }
}
