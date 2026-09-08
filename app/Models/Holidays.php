<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holidays extends Model
{
    use HasFactory;

    protected $table = 'holidays';
    public $timestamps = false;

    protected $fillable = [
        'doctor',
        'date',
        'x',
        'y',
    ];
}
