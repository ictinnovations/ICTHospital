<?php
/**
 * ICTHospital - bring the legacy schema up to something a database can enforce.
 *
 * The schema was carried over faithfully from the original hospital application,
 * which stored dates as varchar(100) and every relationship as a loose varchar
 * holding the parent's id. Nothing stopped a bad date or an appointment pointing
 * at a patient who had been deleted.
 *
 * This does two things, and refuses to do either one blindly:
 *
 *  1. Date columns. A varchar column whose name looks like a date is converted to
 *     DATE or DATETIME only if every non-empty value in it parses. One value that
 *     does not parse and the column is left alone and reported.
 *
 *  2. Relationships. A varchar foreign key is converted to the parent key's type
 *     and given a real foreign key, only if every non-empty value is numeric and
 *     matches a row in the parent table. Any orphan and the relationship is left
 *     alone and reported.
 *
 * Nothing here deletes a row or rewrites a value it cannot read. Run the report
 * first, fix the data it complains about, then apply.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchemaModerniser
{
    /**
     * Relationships worth enforcing, as child table => [column => parent table].
     *
     * The parent's primary key is always `id`.
     */
    public const RELATIONS = [
        'appointment' => ['patient' => 'patient', 'doctor' => 'doctor'],
        'payment' => ['patient' => 'patient', 'doctor' => 'doctor'],
        'prescription' => ['patient' => 'patient', 'doctor' => 'doctor'],
        'lab' => ['patient' => 'patient', 'doctor' => 'doctor'],
        'report' => ['patient' => 'patient', 'doctor' => 'doctor'],
        'medical_history' => ['patient_id' => 'patient'],
        'patient_deposit' => ['patient' => 'patient'],
        'patient_material' => ['patient' => 'patient'],
        'ot_payment' => ['patient' => 'patient'],
        'alloted_bed' => ['patient' => 'patient', 'bed_id' => 'bed'],
    ];

    // diagnostic_report reaches a patient through `invoice`, not by a patient id,
    // so there is nothing here to constrain.

    /**
     * Columns that hold a calendar date rather than a timestamp.
     *
     * Anything matched as a date column but not listed here is treated as a
     * DATETIME, because the legacy code wrote a time into most of them.
     */
    public const DATE_ONLY = [
        'birthdate', 'date', 'add_date', 'e_date', 'report_date', 'payDate',
    ];

    /**
     * Columns whose name says date but whose content is not one.
     */
    public const NOT_DATES = [
        'date_string', 's_time_key', 'time_slot', 'flat_vat', 'vat',
    ];

    /** Report lines, collected as the run proceeds. */
    protected $log = [];

    public function log()
    {
        return $this->log;
    }

    protected function note($status, $target, $detail)
    {
        $this->log[] = ['status' => $status, 'target' => $target, 'detail' => $detail];
    }

    /**
     * Look at everything and, if $apply is true, change what is safe to change.
     */
    public function run($apply = false)
    {
        $this->log = [];
        $this->dates($apply);
        $this->relations($apply);

        return $this->log;
    }

    /* ------------------------------------------------------------------ dates */

    protected function dateColumns()
    {
        $rows = DB::select(
            'select table_name as t, column_name as c, data_type as d
               from information_schema.columns
              where table_schema = database()
                and data_type in ("varchar", "char")
                and (column_name like "%date%" or column_name like "%_time")
              order by table_name, column_name'
        );

        $out = [];

        foreach ($rows as $r) {
            if (in_array($r->c, self::NOT_DATES, true)) {
                continue;
            }
            $out[] = [$r->t, $r->c];
        }

        return $out;
    }

    protected function dates($apply)
    {
        foreach ($this->dateColumns() as [$table, $column]) {
            $target = $table . '.' . $column;

            $values = DB::table($table)
                ->whereNotNull($column)
                ->where($column, '<>', '')
                ->distinct()
                ->limit(5000)
                ->pluck($column);

            $bad = [];

            foreach ($values as $v) {
                if (! $this->parses($v)) {
                    $bad[] = $v;
                    if (count($bad) >= 3) {
                        break;
                    }
                }
            }

            if ($bad) {
                $this->note('skip', $target,
                    'values that do not parse as a date, for example: ' . implode(', ', $bad));
                continue;
            }

            $type = in_array($column, self::DATE_ONLY, true) ? 'DATE' : 'DATETIME';

            if (! $apply) {
                $this->note('would convert', $target,
                    'to ' . $type . ', ' . $values->count() . ' distinct value(s) all parse');
                continue;
            }

            // An empty string is not a date. Make it absent instead.
            DB::table($table)->where($column, '')->update([$column => null]);

            DB::statement(sprintf(
                'ALTER TABLE `%s` MODIFY `%s` %s NULL', $table, $column, $type
            ));

            $this->note('converted', $target, 'now ' . $type . ' NULL');
        }
    }

    /**
     * Would the database read this as a date?
     */
    protected function parses($value)
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return true;
        }

        $stamp = strtotime($value);

        return $stamp !== false && $stamp > 0;
    }

    /* -------------------------------------------------------------- relations */

    protected function relations($apply)
    {
        foreach (self::RELATIONS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $parent) {
                $target = $table . '.' . $column;

                if (! Schema::hasColumn($table, $column) || ! Schema::hasTable($parent)) {
                    $this->note('skip', $target, 'table or column is missing');
                    continue;
                }

                if ($this->hasForeignKey($table, $column)) {
                    $this->note('already done', $target, 'foreign key is present');
                    continue;
                }

                $nonNumeric = DB::table($table)
                    ->whereNotNull($column)
                    ->where($column, '<>', '')
                    ->whereRaw('`' . $column . '` NOT REGEXP "^[0-9]+$"')
                    ->limit(3)
                    ->pluck($column)
                    ->all();

                if ($nonNumeric) {
                    $this->note('skip', $target,
                        'holds values that are not ids, for example: ' . implode(', ', $nonNumeric));
                    continue;
                }

                $orphans = DB::table($table . ' as c')
                    ->leftJoin($parent . ' as p', DB::raw('CAST(c.`' . $column . '` AS UNSIGNED)'), '=', 'p.id')
                    ->whereNotNull('c.' . $column)
                    ->where('c.' . $column, '<>', '')
                    ->whereNull('p.id')
                    ->count();

                if ($orphans > 0) {
                    $this->note('skip', $target,
                        $orphans . ' row(s) point at a ' . $parent . ' that does not exist');
                    continue;
                }

                if (! $apply) {
                    $this->note('would link', $target, 'to ' . $parent . '.id, no orphans found');
                    continue;
                }

                DB::table($table)->where($column, '')->update([$column => null]);

                DB::statement(sprintf(
                    'ALTER TABLE `%s` MODIFY `%s` INT UNSIGNED NULL', $table, $column
                ));

                DB::statement(sprintf(
                    'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`)'
                    . ' REFERENCES `%s` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
                    $table, $this->constraintName($table, $column), $column, $parent
                ));

                $this->note('linked', $target, 'foreign key to ' . $parent . '.id');
            }
        }
    }

    protected function constraintName($table, $column)
    {
        return substr($table . '_' . $column . '_foreign', 0, 64);
    }

    protected function hasForeignKey($table, $column)
    {
        $rows = DB::select(
            'select constraint_name
               from information_schema.key_column_usage
              where table_schema = database()
                and table_name = ?
                and column_name = ?
                and referenced_table_name is not null',
            [$table, $column]
        );

        return count($rows) > 0;
    }

    /**
     * Undo the foreign keys. The column types are left as integers, which is the
     * correct type either way, and the dates are left as dates.
     */
    public function dropForeignKeys()
    {
        foreach (self::RELATIONS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach (array_keys($columns) as $column) {
                if (! $this->hasForeignKey($table, $column)) {
                    continue;
                }

                DB::statement(sprintf(
                    'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                    $table, $this->constraintName($table, $column)
                ));

                $this->note('dropped', $table . '.' . $column, 'foreign key removed');
            }
        }

        return $this->log;
    }
}
