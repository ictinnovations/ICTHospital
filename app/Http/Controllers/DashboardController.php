<?php

/**
 * ICTHospital dashboard controller.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hospital dashboard.
 *
 * Replaces the school dashboard inherited from ICTSchool. Every figure below is
 * read from the hospital schema. Counts are defensive: the tables exist from the
 * migrations but may be empty on a fresh install, and several date columns are
 * still varchar in the legacy schema, so date filtering is done in SQL only where
 * the column format is known to be sortable.
 */
class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        $error   = \Session::get('error');
        $success = \Session::get('success');

        // ---- headline counts -------------------------------------------------
        $totalPatients   = $this->count('patient');
        $totalDoctors    = $this->count('doctor');
        $totalNurses     = $this->count('nurse');
        $totalStaff      = $totalDoctors + $totalNurses
                         + $this->count('receptionist')
                         + $this->count('pharmacist')
                         + $this->count('laboratorist')
                         + $this->count('accountant');

        // ---- beds ------------------------------------------------------------
        $totalBeds    = $this->count('bed');
        $occupiedBeds = $this->count('alloted_bed');
        $freeBeds     = max($totalBeds - $occupiedBeds, 0);
        $occupancy    = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;

        // ---- appointments ----------------------------------------------------
        $totalAppointments   = $this->count('appointment');
        $pendingAppointments = $this->countWhere('appointment', 'status', 'pending');

        // ---- clinical activity ----------------------------------------------
        $totalPrescriptions = $this->count('prescription');
        $totalLabTests      = $this->count('lab');
        $totalReports       = $this->count('diagnostic_report');
        $totalMedicines     = $this->count('medicine');

        // ---- money -----------------------------------------------------------
        $income   = $this->sum('payment', 'amount_received');
        $expenses = $this->sum('expense', 'amount');
        $balance  = $income - $expenses;

        // ---- recent activity -------------------------------------------------
        $recentPatients     = $this->latest('patient', ['id', 'name', 'phone', 'sex', 'age', 'add_date']);
        $recentAppointments = $this->latest('appointment', ['id', 'patient', 'doctor', 'date', 'time_slot', 'status']);

        return view('dashboard', compact(
            'error', 'success',
            'totalPatients', 'totalDoctors', 'totalNurses', 'totalStaff',
            'totalBeds', 'occupiedBeds', 'freeBeds', 'occupancy',
            'totalAppointments', 'pendingAppointments',
            'totalPrescriptions', 'totalLabTests', 'totalReports', 'totalMedicines',
            'income', 'expenses', 'balance',
            'recentPatients', 'recentAppointments'
        ));
    }

    // ---- helpers -------------------------------------------------------------

    private function count(string $table): int
    {
        return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
    }

    private function countWhere(string $table, string $column, string $value): int
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (int) DB::table($table)->whereRaw('LOWER(' . $column . ') = ?', [$value])->count();
    }

    private function sum(string $table, string $column): float
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0.0;
        }

        // legacy money columns are varchar in places, so cast before summing
        return (float) DB::table($table)->sum(DB::raw('CAST(' . $column . ' AS DECIMAL(15,2))'));
    }

    private function latest(string $table, array $columns, int $limit = 5)
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $present = array_values(array_filter($columns, fn ($c) => Schema::hasColumn($table, $c)));
        if (! $present) {
            return collect();
        }

        return DB::table($table)->select($present)->orderByDesc('id')->limit($limit)->get();
    }
}
