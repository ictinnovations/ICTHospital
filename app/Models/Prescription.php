<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescription';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'patient',
        'doctor',
        'symptom',
        'advice',
        'state',
        'dd',
        'medicine',
        'validity',
        'note',
    ];

    /**
     * The drugs written on this prescription.
     *
     * The `medicine` column on this table is the legacy free text summary and is
     * kept in step for anything still reading it, but these rows are the record.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class, 'prescription_id');
    }
}
