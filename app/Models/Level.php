<?php
namespace App\Models;
class Level extends \Eloquent {
	protected $table = 'level';
	 public $timestamps = false;
	protected $fillable = ['name','description'];
}
