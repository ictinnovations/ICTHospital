<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';
    public $timestamps = false;

    protected $fillable = [
        'patient',
        'doctor',
        'date',
        'time_slot',
        's_time',
        'e_time',
        'remarks',
        'add_date',
        'registration_time',
        's_time_key',
        'status',
        'user',
        'request',
        'b_p',
        'pulse',
        'temprature',
        'weight',
    ];
}
