<?php

/**
 * ICTHospital global search controller.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Global search across hospital records.
 *
 * Replaces the school search inherited from ICTSchool, which queried the Student
 * table and never returned its results. Searches patients and doctors, and only
 * against columns that actually exist, so a schema change cannot produce a 500.
 */
class SearchController extends Controller
{
    private const LIMIT = 25;

    public function index()
    {
        return view('search.search');
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->input('search', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([
                'term' => $term,
                'patients' => [],
                'doctors' => [],
                'message' => 'Enter at least two characters.',
            ]);
        }

        $patients = $this->lookup('patient', ['name', 'patient_id', 'phone', 'email', 'address', 'bloodgroup'], $term, [
            'id', 'name', 'patient_id', 'phone', 'sex', 'age', 'bloodgroup',
        ]);

        $doctors = $this->lookup('doctor', ['name', 'phone', 'email', 'department', 'specialist'], $term, [
            'id', 'name', 'phone', 'email',
        ]);

        return response()->json([
            'term' => $term,
            'patients' => $patients,
            'doctors' => $doctors,
            'count' => count($patients) + count($doctors),
        ]);
    }

    /**
     * Search $table across whichever of $searchable exist, returning $select columns.
     */
    private function lookup(string $table, array $searchable, string $term, array $select): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $cols = array_values(array_filter($searchable, fn ($c) => Schema::hasColumn($table, $c)));
        if (! $cols) {
            return [];
        }

        $selectable = array_values(array_filter($select, fn ($c) => Schema::hasColumn($table, $c)));

        $query = DB::table($table)->select($selectable);
        $query->where(function ($q) use ($cols, $term) {
            foreach ($cols as $i => $col) {
                $i === 0
                    ? $q->where($col, 'LIKE', '%' . $term . '%')
                    : $q->orWhere($col, 'LIKE', '%' . $term . '%');
            }
        });

        return $query->limit(self::LIMIT)->get()->toArray();
    }
}
