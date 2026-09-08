<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Featured extends Model
{
    use HasFactory;

    protected $table = 'featured';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'name',
        'profile',
        'description',
    ];
}
