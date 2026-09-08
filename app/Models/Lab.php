<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    use HasFactory;

    protected $table = 'lab';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'patient',
        'doctor',
        'date',
        'category_name',
        'report',
        'status',
        'user',
        'patient_name',
        'patient_phone',
        'patient_address',
        'doctor_name',
        'date_string',
        'invoice_id',
        'report_date',
        'cnic',
        'token_no',
    ];
}
