<?php
/**
 * ICTHospital - remind patients about an upcoming appointment.
 *
 * Replaces attendanceNotification from ICTSchool, which chased absent students and
 * texted their parents. A hospital does not take attendance, it books appointments,
 * so the same ICTCore plumbing now sends the patient a reminder for the day.
 *
 * Usage:
 *   php artisan hospital:appointment-reminder            reminds about tomorrow
 *   php artisan hospital:appointment-reminder --days=0   reminds about today
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Console\Commands;

use App\Http\Controllers\ICTCoreController;
use App\Models\Ictcore_attendance;
use App\Models\Ictcore_integration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppointmentReminder extends Command
{
    protected $signature = 'hospital:appointment-reminder {--days=1 : how many days ahead to remind about}';

    protected $description = 'Send an SMS reminder to every patient with an appointment on the target day';

    /** Contacts pushed to ICTCore in a single run. */
    protected $batchLimit = 200;

    /** Local numbers starting with a single 0 get this country code instead. */
    protected $countryCode = '92';

    public function handle()
    {
        $target = Carbon::now()->addDays((int) $this->option('days'))->format('Y-m-d');

        foreach (['appointment', 'patient', 'notification_type', 'ictcore_integration'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn('Skipped: table ' . $table . ' is missing.');

                return self::SUCCESS;
            }
        }

        $notification = DB::table('notification_type')
            ->whereIn('notification', ['appointment', 'attendance'])
            ->first();

        if (! $notification || empty($notification->type)) {
            $this->warn('Skipped: no notification type is configured for appointments.');

            return self::SUCCESS;
        }

        $integration = Ictcore_integration::where('type', $notification->type)->first();

        if (! $integration) {
            $this->warn('Skipped: no ICTCore integration for type ' . $notification->type . '.');

            return self::SUCCESS;
        }

        $appointments = $this->appointmentsOn($target);

        if ($appointments->isEmpty()) {
            $this->info('Nothing to send. No appointments on ' . $target . '.');

            return self::SUCCESS;
        }

        $ict = new ICTCoreController();
        $telenor = isset($integration->method) && $integration->method === 'telenor';

        $groupId = $telenor
            ? $ict->telenor_apis('group', '', '', '', '', '')
            : $this->createGroup($ict, $integration, $target);

        if (! $groupId) {
            $this->warn('Skipped: ICTCore did not return a contact group.');

            return self::SUCCESS;
        }

        $numbers = [];

        foreach ($appointments as $row) {
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
            $this->warn('Skipped: no usable phone numbers on those appointments.');

            return self::SUCCESS;
        }

        if ($telenor) {
            $ict->telenor_apis('add_contact', $groupId, implode(',', $numbers), '', '', '');
            $settings = Ictcore_attendance::first();
            $ict->telenor_apis(
                'campaign_create',
                $groupId,
                '',
                $this->message($target),
                $settings ? $settings->telenor_file_id : '',
                $notification->type
            );
        } else {
            $this->createCampaign($ict, $integration, $notification, $groupId, $target);
        }

        $this->info('Reminder queued for ' . count($numbers) . ' patient(s) with appointments on ' . $target . '.');

        return self::SUCCESS;
    }

    /**
     * Appointments on one day, with the patient's contact details.
     *
     * `appointment.date` is varchar in the legacy schema, so it is compared as a
     * date rather than as a string.
     */
    protected function appointmentsOn($target)
    {
        $query = DB::table('appointment')
            ->select([
                DB::raw('patient.name as name'),
                DB::raw('patient.phone as phone'),
                DB::raw('patient.email as email'),
            ])
            ->join('patient', 'patient.id', '=', 'appointment.patient')
            ->whereRaw('DATE(appointment.date) = ?', [$target])
            ->limit($this->batchLimit);

        if (Schema::hasColumn('appointment', 'status')) {
            $query->where(function ($q) {
                $q->whereNull('appointment.status')
                  ->orWhereNotIn('appointment.status', ['cancelled', 'Cancelled', 'completed', 'Completed']);
            });
        }

        return $query->get();
    }

    protected function createGroup(ICTCoreController $ict, $integration, $target)
    {
        $configured = ! empty($integration->ictcore_url)
            && ! empty($integration->ictcore_user)
            && ! empty($integration->ictcore_password);

        if (! $configured) {
            return null;
        }

        return $ict->ictcore_api('groups', 'POST', [
            'name' => 'Appointment reminder ' . $target,
            'description' => 'Patients with an appointment on ' . $target,
        ]);
    }

    protected function createCampaign(ICTCoreController $ict, $integration, $notification, $groupId, $target)
    {
        $settings = Ictcore_attendance::first();
        $programId = $settings ? $settings->ictcore_program_id : null;

        if ($notification->type === 'sms' || empty($programId)) {
            $textId = $ict->ictcore_api('messages/texts', 'POST', [
                'name' => 'appointment_reminder',
                'data' => $this->message($target),
                'type' => 'utf-8',
                'description' => '',
            ]);

            $programId = $ict->ictcore_api('programs/sendsms', 'POST', [
                'name' => 'appointment_reminder',
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

    protected function message($target)
    {
        $settings = Ictcore_attendance::first();

        if ($settings && ! empty($settings->description)) {
            return $settings->description;
        }

        return 'Reminder: you have an appointment at our hospital on ' . $target . '. Please arrive ten minutes early.';
    }

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
