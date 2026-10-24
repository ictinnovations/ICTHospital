<?php
/**
 * ICTHospital - what one doctor is owed for one invoice line.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorShare extends Model
{
    protected $table = 'doctor_share';

    protected $fillable = [
        'payment_id', 'invoice_item_id', 'doctor_id', 'role', 'share_rule_id', 'base_amount', 'percent', 'amount',
        'status', 'approved_by', 'approved_at', 'paid_at', 'payout_ref',
    ];

    protected $casts = ['base_amount' => 'decimal:2', 'percent' => 'decimal:2', 'amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }
}
