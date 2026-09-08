<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratorist extends Model
{
    use HasFactory;

    protected $table = 'laboratorist';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'address',
        'phone',
        'x',
        'y',
        'ion_user_id',
    ];
}
