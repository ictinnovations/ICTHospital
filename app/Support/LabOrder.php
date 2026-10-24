<?php
/**
 * ICTHospital - raises a lab request from code (tests ordered during a consultation).
 *
 * Writes the same rows and legacy summary columns as the lab request screen.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\Doctor;
use App\Models\Lab;
use App\Models\LabCategory;
use App\Models\LabTest;
use App\Models\Patient;

class LabOrder
{
    public static function create(Patient $patient, $doctorId, array $categoryIds)
    {
        $categoryIds = array_values(array_unique(array_map('intval', $categoryIds)));
        $catalogue = LabCategory::whereIn('id', $categoryIds)->get()->keyBy('id');
        if ($catalogue->isEmpty()) {
            return null;
        }

        $doctor = $doctorId ? Doctor::find($doctorId) : null;
        $lab = Lab::create([
            'patient' => $patient->id, 'doctor' => $doctorId, 'date' => now()->toDateString(), 'date_string' => now()->toDateString(),
            'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'patient_address' => $patient->address,
            'doctor_name' => $doctor->name ?? null, 'category' => null, 'category_name' => null, 'report' => null,
            'status' => 'requested', 'user' => (string) (auth()->id() ?? ''),
        ]);

        foreach ($categoryIds as $id) {
            if ($c = $catalogue->get($id)) {
                LabTest::create(['lab_id' => $lab->id, 'lab_category_id' => $c->id, 'name' => $c->category,
                    'reference_value' => $c->reference_value, 'result' => null, 'status' => 'requested']);
            }
        }

        $tests = $lab->fresh('tests')->tests;
        $lab->update([
            'category' => $tests->pluck('lab_category_id')->filter()->implode(','),
            'category_name' => substr($tests->pluck('name')->implode(', '), 0, 1000),
            'report' => substr($tests->map->summary()->implode("\n"), 0, 10000),
        ]);

        return $lab;
    }
}
