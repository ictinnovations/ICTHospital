<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $table = 'bed';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'number',
        'description',
        'last_a_time',
        'last_d_time',
        'status',
        'bed_id',
    ];
}
