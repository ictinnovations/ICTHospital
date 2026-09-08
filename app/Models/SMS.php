<?php
namespace App\Models;

class SMS extends \Eloquent {
    protected $table = 'smsFormat';
    protected $fillable = ['type','sender','message'];
}