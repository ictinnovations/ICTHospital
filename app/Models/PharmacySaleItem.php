<?php
/**
 * ICTHospital - one line on a pharmacy sale.
 *
 * The name and unit price are stored here rather than read back from the
 * catalogue. A sale is a financial record of what was handed over and what was
 * charged on the day, and it must not change when the catalogue price is next
 * updated.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacySaleItem extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_sale_item';
    public $timestamps = false;

    protected $fillable = [
        'pharmacy_payment_id',
        'medicine_id',
        'name',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PharmacyPayment::class, 'pharmacy_payment_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }

    public function summary(): string
    {
        return $this->name . ' x' . $this->quantity;
    }
}
