<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    use HasFactory;

    protected $table = 'lab';
    public $timestamps = false;

    protected $fillable = [
        'category',
        'patient',
        'doctor',
        'date',
        'category_name',
        'report',
        'status',
        'user',
        'patient_name',
        'patient_phone',
        'patient_address',
        'doctor_name',
        'date_string',
        'invoice_id',
        'report_date',
        'cnic',
        'token_no',
    ];

    /**
     * The tests on this request.
     *
     * `category_name` and `report` on this table are the legacy packed summaries
     * and are kept in step for anything still reading them, but these rows are
     * the record.
     */
    public function tests(): HasMany
    {
        return $this->hasMany(LabTest::class, 'lab_id');
    }
}
