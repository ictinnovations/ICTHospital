<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtPayment extends Model
{
    use HasFactory;

    protected $table = 'ot_payment';
    public $timestamps = false;

    protected $fillable = [
        'patient',
        'doctor_c_s',
        'doctor_a_s_1',
        'doctor_a_s_2',
        'doctor_anaes',
        'n_o_o',
        'c_s_f',
        'a_s_f_1',
        'a_s_f_2',
        'anaes_f',
        'ot_charge',
        'cab_rent',
        'seat_rent',
        'others',
        'discount',
        'date',
        'amount',
        'doctor_fees',
        'hospital_fees',
        'gross_total',
        'flat_discount',
        'amount_received',
        'status',
        'user',
    ];
}
