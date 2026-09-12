<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';
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
        'remarks',
        'hospital_amount',
        'doctor_amount',
        'category_amount',
        'category_name',
        'amount_received',
        'deposit_type',
        'status',
        'user',
        'patient_name',
        'patient_phone',
        'patient_address',
        'doctor_name',
        'date_string',
    ];

    /** What was charged. category_name and category_amount are the legacy copies. */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'payment_id');
    }

    /** What has been paid, possibly in several instalments. */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class, 'payment_id');
    }

    public function paid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balance(): float
    {
        return round((float) $this->gross_total - $this->paid(), 2);
    }

    /**
     * Derived, never typed.
     *
     * Nothing paid is unpaid, something paid is part paid, the whole total is
     * paid. A status column set by hand is the first thing to go stale on an
     * invoice that gets part settled.
     */
    public function settlement(): string
    {
        $paid = $this->paid();

        if ($paid <= 0) {
            return 'unpaid';
        }

        return $this->balance() > 0.004 ? 'part paid' : 'paid';
    }
}
