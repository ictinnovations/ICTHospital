<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $table = 'paymentGateway';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'merchant_key',
        'salt',
        'x',
        'y',
        'APIUsername',
        'APIPassword',
        'APISignature',
        'status',
    ];
}
