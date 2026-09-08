<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'membership';
    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'membership_type',
        'date',
        'expiry',
        'status',
        'ion_user_id',
        'card_no',
    ];
}
