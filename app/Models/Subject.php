<?php
namespace App\Models;
class Subject extends \Eloquent {
	protected $table = 'Subject';
protected $fillable = ['name','description','class','gradeSystem'];
}
