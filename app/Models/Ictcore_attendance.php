<?php
namespace App\Models;
class Ictcore_attendance extends \Eloquent {
	protected $table = 'ictcore_attendance';
	protected $fillable = ['name','description','recording','ictcore_recording_id','ictcore_program_id'];
}
