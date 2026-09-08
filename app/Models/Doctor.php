<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctor';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'address',
        'phone',
        'department',
        'profile',
        'x',
        'y',
        'ion_user_id',
        'procedure_id',
    ];
}
