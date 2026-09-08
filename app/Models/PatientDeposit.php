<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDeposit extends Model
{
    use HasFactory;

    protected $table = 'patient_deposit';
    public $timestamps = false;

    protected $fillable = [
        'patient',
        'payment_id',
        'date',
        'deposited_amount',
        'amount_received_id',
        'deposit_type',
        'gateway',
        'user',
    ];
}
