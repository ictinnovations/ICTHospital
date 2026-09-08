<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllotedBed extends Model
{
    use HasFactory;

    protected $table = 'alloted_bed';
    public $timestamps = false;

    protected $fillable = [
        'number',
        'category',
        'patient',
        'a_time',
        'd_time',
        'status',
        'x',
        'bed_id',
    ];
}
