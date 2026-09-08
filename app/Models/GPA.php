<?php
namespace App\Models;
class GPA extends \Eloquent {

	protected $table = 'GPA';
protected $fillable = ['for','gpa','grade','markfrom','markto'];
}
