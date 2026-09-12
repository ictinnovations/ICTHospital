<?php
/**
 * ICTHospital - one test on one lab request.
 *
 * The reference range is copied onto the row rather than only referenced, because
 * a result is only interpretable against the range that applied when it was
 * measured, and laboratories revise their ranges.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTest extends Model
{
    use HasFactory;

    protected $table = 'lab_test';
    public $timestamps = false;

    protected $fillable = [
        'lab_id',
        'lab_category_id',
        'name',
        'reference_value',
        'result',
        'status',
        'reported_at',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabCategory::class, 'lab_category_id');
    }

    public function isReported(): bool
    {
        return $this->result !== null && $this->result !== '';
    }

    /** One line as it reads on the report sheet. */
    public function summary(): string
    {
        return trim($this->name . ': ' . ($this->isReported() ? $this->result : 'pending'));
    }
}
