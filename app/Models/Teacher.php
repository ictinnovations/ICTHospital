<?php
namespace App\Models;
class Teacher extends \Eloquent {
	protected $table = 'teacher';
	 public $timestamps = false;
	protected $fillable = [
	'firstName',
	'lastName',
	'gender',
	'religion',
	'bloodgroup',
	'nationality',
	'dob',
	'photo',
	'phone',
	'email',
	'fatherName',
	'fatherCellNo',
	'presentAddress',
	'parmanentAddress'
];

protected $primaryKey = 'id';

}