<?php
namespace App\Models;
class Marks extends \Eloquent {
  protected $table = 'Marks';
	protected $fillable = ['regiNo',
	'regiNo',
	'exam',
	'subject',
	'written',
	'mcq',
	'practical',
	'ca'
	];
}
