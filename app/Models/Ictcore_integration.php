<?php
namespace App\Models;
class Ictcore_integration extends \Eloquent {
	protected $table = 'ictcore_integration';
	protected $fillable = ['ictcore_url','ictcore_user','ictcore_password'];
}
