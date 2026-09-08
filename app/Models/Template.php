<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $table = 'template';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'template',
        'user',
        'x',
        'procedure_id',
    ];
}
