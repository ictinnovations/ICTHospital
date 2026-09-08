<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $table = 'time_slot';
    public $timestamps = false;

    protected $fillable = [
        'doctor',
        's_time',
        'e_time',
        'weekday',
        's_time_key',
    ];
}
