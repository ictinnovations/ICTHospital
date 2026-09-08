<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $table = 'slide';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'img_url',
        'text1',
        'text2',
        'text3',
        'position',
        'status',
    ];
}
