<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientMaterial extends Model
{
    use HasFactory;

    protected $table = 'patient_material';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'title',
        'category',
        'patient',
        'patient_name',
        'patient_address',
        'patient_phone',
        'url',
        'date_string',
    ];
}
