<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $table = 'donor';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'group',
        'age',
        'sex',
        'ldd',
        'phone',
        'email',
        'add_date',
    ];
}
