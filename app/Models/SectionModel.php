<?php
namespace App\Models;
class SectionModel extends \Eloquent {

	 protected $table = 'section';
	 public $timestamps = false;
	protected $fillable = ['name','description','class_code'];

}
