<?php
/**
 * ICTHospital - one radiology exam ordered for a patient, and its report.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyOrder extends Model
{
    protected $table = 'radiology_order';

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_id', 'radiology_exam_id', 'name', 'modality', 'urgent', 'clinical_notes',
        'status', 'scheduled_at', 'performed_at', 'findings', 'impression', 'reported_by', 'reported_at', 'payment_id', 'created_by',
    ];

    protected $casts = ['urgent' => 'boolean'];

    public const STATUSES = ['ordered' => 'Ordered', 'scheduled' => 'Scheduled', 'done' => 'Done, awaiting report', 'reported' => 'Reported', 'cancelled' => 'Cancelled'];

    /** Orders a set of catalogue exams for a patient. Returns the created orders. */
    public static function place(Patient $patient, $doctorId, array $examIds, $urgent = false, $notes = null, $appointmentId = null)
    {
        $out = [];
        foreach (RadiologyExam::whereIn('id', array_unique(array_map('intval', $examIds)))->get() as $exam) {
            $out[] = static::create([
                'patient_id' => $patient->id, 'doctor_id' => $doctorId, 'appointment_id' => $appointmentId,
                'radiology_exam_id' => $exam->id, 'name' => $exam->name, 'modality' => $exam->modality,
                'urgent' => (bool) $urgent, 'clinical_notes' => $notes, 'status' => 'ordered', 'created_by' => auth()->id(),
                'findings' => $exam->report_template,
            ]);
        }

        return $out;
    }
}
