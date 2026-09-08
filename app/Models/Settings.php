<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    public $timestamps = false;

    protected $fillable = [
        'system_vendor',
        'title',
        'address',
        'phone',
        'email',
        'facebook_id',
        'currency',
        'language',
        'discount',
        'vat',
        'login_title',
        'logo',
        'invoice_logo',
        'payment_gateway',
        'sms_gateway',
        'codec_username',
        'codec_purchase_code',
    ];
}
