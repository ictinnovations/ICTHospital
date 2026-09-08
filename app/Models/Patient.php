<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'doctor',
        'address',
        'phone',
        'sex',
        'birthdate',
        'age',
        'bloodgroup',
        'ion_user_id',
        'patient_id',
        'add_date',
        'registration_time',
        'how_added',
        'father_name',
    ];
}
