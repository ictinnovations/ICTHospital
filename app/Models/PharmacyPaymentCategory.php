<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyPaymentCategory extends Model
{
    use HasFactory;

    protected $table = 'pharmacy_payment_category';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'description',
        'c_price',
        'd_commission',
        'h_commission',
    ];
}
