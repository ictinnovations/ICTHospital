<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';
    public $timestamps = false;

    protected $fillable = [
        'patient',
        'doctor',
        'date',
        'time_slot',
        's_time',
        'e_time',
        'remarks',
        'add_date',
        'registration_time',
        's_time_key',
        'status',
        'user',
        'request',
        'b_p',
        'pulse',
        'temprature',
        'weight',
        'token_no',
        'checked_in_at',
        'consult_started_at',
        'checked_out_at',
        'visit_type',
        'payment_id',
        'mode',
        'video_room',
        'branch_id',
    ];

    /** New rows are stamped with the branch the user is working at. */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->branch_id) && \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(), 'branch_id')) {
                $model->branch_id = \App\Support\Branch::current();
            }
        });
    }
}
