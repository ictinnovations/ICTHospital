<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescription';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'patient',
        'doctor',
        'symptom',
        'advice',
        'state',
        'dd',
        'medicine',
        'validity',
        'note',
    ];
}
