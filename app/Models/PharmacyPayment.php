<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
