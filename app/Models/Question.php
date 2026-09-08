<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
   protected $fillable = [
        'section_id',
        'class_code',
        'subject_id',
        'chapter',
        'session',
        'quize_name',
        'level',
        'logo',
        'question_name',
        'question_type',
        'choices',
        'answer',
        'points'
    ];
}
