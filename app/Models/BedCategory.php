<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedCategory extends Model
{
    use HasFactory;

    protected $table = 'bed_category';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'description',
    ];
}
