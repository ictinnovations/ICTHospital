<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
