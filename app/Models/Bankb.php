<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankb extends Model
{
    use HasFactory;

    protected $table = 'bankb';
    public $timestamps = false;

    protected $fillable = [
        'group',
        'status',
    ];
}
