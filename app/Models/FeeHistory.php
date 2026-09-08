<?php
namespace App\Models;
class FeeHistory extends \Eloquent {
	protected $table = 'billHistory';
	protected $fillable = ['billNo','title','month','fee','lateFee','total'];
	public $timestamps = false;
}
