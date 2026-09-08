<?php
namespace App\Models;
//ade3el butth
class Schedule extends \Eloquent {
	protected $table = 'cronschedule';
	 //public $timestamps = r;
	protected $fillable = ['date','time'];
}
