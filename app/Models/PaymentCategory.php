<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCategory extends Model
{
    use HasFactory;

    protected $table = 'payment_category';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'description',
        'c_price',
        'type',
        'd_commission',
        'h_commission',
    ];
}
