<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSchedule extends Model
{
    use HasFactory;

    protected $table = 'time_schedule';
    public $timestamps = false;

    protected $fillable = [
        'doctor',
        'weekday',
        's_time',
        'e_time',
        's_time_key',
        'duration',
    ];
}
