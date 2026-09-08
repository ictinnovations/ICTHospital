<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipSettings extends Model
{
    use HasFactory;

    protected $table = 'membership_settings';
    public $timestamps = false;

    protected $fillable = [
        'membership_name',
        'membership_price',
        'expiry',
        'discount',
    ];
}
