<?php
namespace App\Models;
class Message extends \Eloquent {
	protected $table = 'message';
	 public $timestamps = false;
	protected $fillable = ['name','description','recording'];
}
