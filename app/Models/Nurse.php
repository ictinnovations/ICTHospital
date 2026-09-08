<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    use HasFactory;

    protected $table = 'nurse';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'address',
        'phone',
        'x',
        'y',
        'z',
        'ion_user_id',
    ];
}
