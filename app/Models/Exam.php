<?php
namespace App\Models;
class Exam extends \Eloquent {
	protected $table = 'exam';
	protected $fillable = ['type','class_id','section_id'];
}
