<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'email';
    public $timestamps = false;

    protected $fillable = [
        'subject',
        'date',
        'message',
        'reciepient',
        'user',
    ];
}
