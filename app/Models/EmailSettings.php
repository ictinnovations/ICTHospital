<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSettings extends Model
{
    use HasFactory;

    protected $table = 'email_settings';
    public $timestamps = false;

    protected $fillable = [
        'admin_email',
        'type',
        'user',
        'password',
    ];
}
