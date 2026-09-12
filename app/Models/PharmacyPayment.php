<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyPayment extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_payment';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'patient',
        'doctor',
        'date',
        'amount',
        'vat',
        'x_ray',
        'flat_vat',
        'discount',
        'flat_discount',
        'gross_total',
        'hospital_amount',
        'doctor_amount',
        'category_amount',
        'category_name',
        'amount_received',
        'status',
    ];

    /**
     * What was dispensed on this sale.
     *
     * category_name and category_amount on this table are the legacy packed
     * columns, kept in step for anything still reading them. These rows are the
     * record, and they are what stock movement is calculated from.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PharmacySaleItem::class, 'pharmacy_payment_id');
    }
}
