<?php
namespace App\Models;
class Accounting extends \Eloquent {
	protected $table = 'accounting';
	protected $fillable = ['name','type','amount','date','description'];
}
