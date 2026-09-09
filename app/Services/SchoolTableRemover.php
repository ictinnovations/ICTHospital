<?php
/**
 * ICTHospital - remove the school tables inherited from ICTSchool.
 *
 * Thirty-three tables came across with the fork and describe a school, not a
 * hospital: students, classes, sections, subjects, exams, marks, GPA rules,
 * timetables, promotion, admissions, library books, dormitories, and the family
 * fee voucher chain of stdBill, billHistory, feesSetup and family_vouchar.
 * Nothing in the application reads any of them any more.
 *
 * A table is only dropped when it is empty. If somebody's install has rows in
 * one, it is left alone and reported, because a migration should not quietly
 * delete data it does not understand. Pass force to drop them regardless.
 *
 * `Holidays` needs care. The school table is `Holidays` and the hospital table
 * is `holidays`, which are different tables on Linux and the same table on a
 * server configured to fold case. The capitalised one is only dropped when the
 * lower case one exists separately, so a case folding install cannot lose its
 * hospital holidays.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchoolTableRemover
{
    /**
     * School tables, in an order that drops children before parents.
     */
    public const TABLES = [
        // fees and vouchers
        'billHistory',
        'stdBill',
        'feesSetup',
        'family_vouchar',
        'voucherhistories',
        'invoice_history',
        'admissionfee_collections',
        // academic records
        'Marks',
        'MeritList',
        'GPA',
        'exam',
        'question_temps',
        'questions',
        'Subject',
        'timetable',
        'section_attendance',
        'Attendance',
        'section',
        'Class',
        'ClassOffDay',
        'acadamic_year',
        // people and places
        'Student',
        'teacher',
        'admission',
        'diaries',
        'referals',
        // library
        'issueBook',
        'bookStock',
        'Books',
        // dormitory
        'dormitory_student',
        'dormitory_fee',
        'dormitory',
    ];

    /**
     * Dropped only when a separate lower case `holidays` exists.
     */
    public const CASE_SENSITIVE = ['Holidays' => 'holidays'];

    protected $log = [];

    protected function note($status, $table, $detail)
    {
        $this->log[] = ['status' => $status, 'table' => $table, 'detail' => $detail];
    }

    public function run($apply = false, $force = false)
    {
        $this->log = [];

        $targets = self::TABLES;

        foreach (self::CASE_SENSITIVE as $school => $hospital) {
            if (! $this->tableExists($school)) {
                continue;
            }

            if ($this->tableExists($hospital) && $this->distinctTables($school, $hospital)) {
                $targets[] = $school;
            } else {
                $this->note('skip', $school,
                    'kept: this server folds table name case, so it is the same table as ' . $hospital);
            }
        }

        // Constraints first, or a drop can fail on a dependency.
        if ($apply) {
            Schema::disableForeignKeyConstraints();
        }

        foreach ($targets as $table) {
            if (! $this->tableExists($table)) {
                $this->note('gone', $table, 'already removed');
                continue;
            }

            $rows = DB::table($table)->count();

            if ($rows > 0 && ! $force) {
                $this->note('skip', $table, $rows . ' row(s) present, left alone');
                continue;
            }

            if (! $apply) {
                $this->note('would drop', $table,
                    $rows > 0 ? $rows . ' row(s), forced' : 'empty');
                continue;
            }

            Schema::drop($table);
            $this->note('dropped', $table, $rows > 0 ? $rows . ' row(s) discarded' : 'was empty');
        }

        if ($apply) {
            Schema::enableForeignKeyConstraints();
        }

        return $this->log;
    }

    protected function tableExists($table)
    {
        return count(DB::select(
            'select 1 from information_schema.tables
              where table_schema = database() and table_name = ? limit 1',
            [$table]
        )) > 0;
    }

    /**
     * True when the server really does hold these as two separate tables.
     *
     * Matching on lower(table_name) and counting the rows. Comparing table_name
     * against a list folds case in information_schema and answers 1 even when
     * both tables are physically present, so it cannot be used to tell them
     * apart.
     */
    protected function distinctTables($a, $b)
    {
        $rows = DB::select(
            'select count(*) as n from information_schema.tables
              where table_schema = database() and lower(table_name) = lower(?)',
            [$a]
        );

        unset($b);

        return isset($rows[0]) && (int) $rows[0]->n === 2;
    }
}
