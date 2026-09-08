<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';
    public $timestamps = false;

    protected $fillable = [
        'img_url',
        'title',
        'description',
    ];
}
