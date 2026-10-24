<?php
/**
 * ICTHospital - the doctor's note for one OPD visit.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultation';

    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id', 'date', 'presenting_complaint', 'history', 'examination',
        'provisional_diagnosis', 'diagnosis', 'plan', 'treatment', 'follow_up_date', 'follow_up_notes', 'allergies',
        'past_history', 'social_history', 'specialty', 'immunizations', 'status', 'completed_at', 'created_by',
    ];

    protected $casts = [
        'examination' => 'array', 'past_history' => 'array', 'social_history' => 'array',
        'specialty' => 'array', 'immunizations' => 'array',
    ];

    /** Body systems on the examination grid. */
    public const SYSTEMS = [
        'gpe' => 'General physical examination', 'heent' => 'HEENT', 'cvs' => 'Cardiovascular', 'chest' => 'Chest and lungs',
        'abdomen' => 'Abdomen', 'cns' => 'Neurological', 'msk' => 'Musculoskeletal', 'skin' => 'Skin',
    ];

    public const PAST = ['diabetes' => 'Diabetes', 'hypertension' => 'Hypertension', 'asthma' => 'Asthma', 'ihd' => 'Ischaemic heart disease',
        'epilepsy' => 'Epilepsy', 'bleeding' => 'Bleeding disorder', 'tb' => 'Tuberculosis', 'hepatitis' => 'Hepatitis'];

    public const VACCINES = ['BCG', 'OPV/IPV', 'Pentavalent (DTP-HepB-Hib)', 'PCV', 'Rotavirus', 'Measles/MR', 'Typhoid', 'Td (adult)', 'Hepatitis B', 'Influenza', 'COVID-19'];

    /** Text sections a template can fill. */
    public const TEMPLATE_FIELDS = ['presenting_complaint', 'history', 'provisional_diagnosis', 'diagnosis', 'plan', 'treatment', 'follow_up_notes'];

    /**
     * Expected date of delivery from the last menstrual period (Naegele's rule:
     * LMP + 280 days) and the gestational age today, in weeks and days.
     */
    public static function obstetricDates($lmp, $on = null)
    {
        if (! $lmp) {
            return [null, null];
        }
        $lmp = \Carbon\Carbon::parse($lmp);
        $on = $on ? \Carbon\Carbon::parse($on) : now();
        $days = (int) $lmp->diffInDays($on, false);

        return [$lmp->copy()->addDays(280)->toDateString(), $days >= 0 ? intdiv($days, 7) . 'w ' . ($days % 7) . 'd' : null];
    }
}
