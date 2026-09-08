<?php
namespace App\Models;
class Ictcore_fees extends \Eloquent {
	protected $table = 'ictcore_fees';
	protected $fillable = ['name','description','recording','ictcore_recording_id','ictcore_program_id'];
}
