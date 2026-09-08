<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountant extends Model
{
    use HasFactory;

    protected $table = 'accountant';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'email',
        'address',
        'phone',
        'x',
        'ion_user_id',
    ];
}
