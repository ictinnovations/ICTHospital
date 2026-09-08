<?php
namespace App\Models;
class Timetable extends \Eloquent {
	 protected $table = 'timetable';
	  public $timestamps = false;
	protected $fillable = ['id','teacher_id','class_id','section_id','subject_id','stattime','endtime','day','color'];

}
