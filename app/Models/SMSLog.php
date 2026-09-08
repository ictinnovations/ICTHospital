<?php
namespace App\Models;
class SMSLog extends \Eloquent {

    protected $table = 'smsLog';
    protected $fillable = ['type','sender','message','recipient','regiNo','status'];
}