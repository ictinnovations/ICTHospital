<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyExpense extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_expense';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'date',
        'amount',
        'user',
    ];
}
