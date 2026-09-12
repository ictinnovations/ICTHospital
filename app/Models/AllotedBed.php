<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllotedBed extends Model
{
    use HasFactory;

    protected $table = 'alloted_bed';
    public $timestamps = false;

    protected $fillable = [
        'number',
        'category',
        'patient',
        'a_time',
        'd_time',
        'status',
        'x',
        'bed_id',
    ];

    /**
     * Admissions that have not ended.
     *
     * A null d_time is what makes a bed occupied. bed.status is kept in step for
     * display, but it is a copy, so anything deciding whether a bed is free asks
     * this instead of reading that column.
     */
    public function scopeOccupied(Builder $query): Builder
    {
        return $query->whereNull('d_time');
    }
}
