<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosticReport extends Model
{
    use HasFactory;

    protected $table = 'diagnostic_report';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'invoice',
        'report',
        'status',
    ];
}
