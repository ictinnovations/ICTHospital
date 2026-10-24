<?php
/**
 * ICTHospital - one closed cashier shift.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftClose extends Model
{
    protected $table = 'shift_close';

    protected $fillable = [
        'user_id', 'opened_at', 'closed_at', 'payments_count', 'total', 'cash', 'bank_transfer', 'card', 'cheque', 'other',
        'counted_cash', 'difference', 'comment', 'closed_by', 'branch_id',
    ];

    protected $casts = [
        'total' => 'decimal:2', 'cash' => 'decimal:2', 'bank_transfer' => 'decimal:2', 'card' => 'decimal:2',
        'cheque' => 'decimal:2', 'other' => 'decimal:2', 'counted_cash' => 'decimal:2', 'difference' => 'decimal:2',
    ];

    /** New rows are stamped with the branch the user is working at. */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->branch_id) && \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'branch_id')) {
                $model->branch_id = \App\Support\Branch::current();
            }
        });
    }
}
