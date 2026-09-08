<?php
namespace App\Models;
class Attendance extends \Eloquent {
  protected $dates = ['date','created_at'];
  protected $table = 'Attendance';
   public $timestamps = false;
  protected $fillable = ['student_regiNo','date','status','created_at'];
  public function student(){
   // return $this->belongsTo('App\Student','regiNo');
  }
}
