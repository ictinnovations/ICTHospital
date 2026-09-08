<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_expense_category';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'description',
        'x',
        'y',
    ];
}
