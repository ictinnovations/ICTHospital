<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSettings extends Model
{
    use HasFactory;

    protected $table = 'sms_settings';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'username',
        'password',
        'api_id',
        'sender',
        'authkey',
        'user',
    ];
}
