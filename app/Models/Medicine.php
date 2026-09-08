<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicine';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'category',
        'price',
        'box',
        's_price',
        'quantity',
        'generic',
        'company',
        'effects',
        'e_date',
        'add_date',
    ];
}
