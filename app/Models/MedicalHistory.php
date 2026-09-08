<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $table = 'medical_history';
    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'title',
        'description',
        'patient_name',
        'patient_address',
        'patient_phone',
        'img_url',
        'date',
        'registration_time',
    ];
}
