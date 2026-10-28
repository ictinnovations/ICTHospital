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

        // A patient who is at the desk has a token, whichever screen marked them arrived.
        // The appointment form offers "Arrived" directly, which used to skip check in and
        // left the visit in the queue with no token.
        static::saving(function ($model) {
            if (in_array($model->status, ['arrived', 'in consultation'], true) && $model->token_no === null && $model->doctor && $model->date) {
                $model->token_no = static::nextToken($model->doctor, $model->date);
                $model->checked_in_at = $model->checked_in_at ?: now();
            }
        });
    }

    /** The next token number for a doctor on a date. Call inside a transaction. */
    public static function nextToken($doctorId, $date)
    {
        $max = static::where('doctor', $doctorId)->whereDate('date', \Carbon\Carbon::parse($date)->toDateString())->lockForUpdate()->max('token_no');

        return ((int) $max) + 1;
    }
}
