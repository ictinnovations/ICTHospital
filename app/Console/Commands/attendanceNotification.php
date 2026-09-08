<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\CronjobController;
use App\Http\Controllers\ICTCoreController;
use App\Student;
use File;
use App\Ictcore_integration;
use App\Ictcore_attendance;
use App\Ictcore_fees;
use App\SectionModel;
use App\ClassModel;
use App\Notification;
use App\SMSLog;
use App\Attendance;
use DB;
use Storage;
use Carbon\Carbon;


class attendanceNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendanceNotification:attendacenotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification sendend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $now        = Carbon::now('Asia/Karachi');
        $year       =  $now->year;
        $status     = array('Absent','Late','late');
         //echo $now->format('H:i:s');
         //exit;
    if(Storage::disk('local')->exists('/public/cronsettings.txt')){
        
        $contant = Storage::get('/public/cronsettings.txt');
        $data    = explode('<br>',$contant );
        $attendance_time = $data[0]; 

        if($now->format('H:i')>=$attendance_time  ){
        
            $previouse_sended_sms = SMSLog::whereDate('created_at', '=', $now->toDateString())->where('status','ok')->orwhere('status','sended')->get();
            $previus_sended_ids   = array();

       foreach($previouse_sended_sms as $get_ids)
       {
         $previus_sended_ids[]  = $get_ids->regiNo;
       }



       // echo "bhutta<pre>".$now->toDateString();print_r( $previouse_sended_sms);exit;
        $attendance = DB::table('Student')
                      ->select('Student.id as student_id','Student.firstName', 'Student.middleName', 'Student.lastName','Student.fatherCellNo','Student.fatherName','Attendance.status','Attendance.regiNo')
                      ->join('Attendance' ,'Student.regiNo', '=' , 'Attendance.regiNo')
                      /*->where('Student.section',  $section_id)->where('Student.session',$year)*/
                      ->where('Attendance.date','=',Carbon::today()->toDateString())
                      ->whereIn('Attendance.status',$status);
                      if(!empty($previus_sended_ids)){
                        $attendance =$attendance->whereNotIn('Attendance.regiNo',$previus_sended_ids);
                      }

        //echo "<pre>";print_r($attendance->get());
       

        if($attendance->count()){
             //$this->info('Notssssification sended ttsuccessfully');    
            $attendance = $attendance->get();
            $attendance_noti     = DB::table('notification_type')->where('notification','attendance')->first();
            $ictcore_attendance  = Ictcore_attendance::select("*")->first();
            $ictcore_integration = Ictcore_integration::select("*")->where('type',$attendance_noti->type)->first();
            //$ictcore_integration = Ictcore_integration::select("*")->first();
             $ict                 = new ICTCoreController();
            
            foreach($attendance as $student)
            { 
                if($ictcore_integration->method=="telenor"){   
                    if($student->status=="Absent"){
                        $get_msg  = DB::table('ictcore_attendance')->first();
                        $name     = $student->firstName.' '.$student->lastName;
                        if($attendance_noti->type=='sms'){
                            $msg      =  str_replace("<<parent>>",$student->fatherName,$get_msg->description);
                            $msg      =  str_replace("<<name>>",$name,$msg);
                        }else{
                               $msg = $get_msg->telenor_file_id;     
                        }
                    }elseif($student->status=="Late" || $student->status=="late"){
                        $get_msg  = DB::table('ictcore_attendance')->first();
                        $name     = $student->firstName.' '.$student->lastName;
                        if($attendance_noti->type=='sms'){
                            $msg      =  str_replace("<<parent>>",$student->fatherName,$get_msg->late_description);
                            $msg      =  str_replace("<<name>>",$name,$msg);
                        }else{
                           $msg = $get_msg->telenor_file_id_late;         
                        }
                    }
                    if (preg_match("~^0\d+$~", $student->fatherCellNo)) {
                        $to = preg_replace('/0/', '92', $student->fatherCellNo, 1);
                    }else {
                        $to =$student->fatherCellNo;  
                    }
                     if(strlen($to)==12){
                        $snd_msg  = $ict->verification_number_telenor_sms($to,$msg,'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,$attendance_noti->type);
                       $snd_msg =  $snd_msg->response;
                       print_r( $snd_msg);

                       }
                    $smsLog = new SMSLog();
                        $smsLog->type      = "Attendance";
                        $smsLog->sender    = "telenor ";
                        $smsLog->message   = $msg;
                        $smsLog->recipient = $student->fatherCellNo;
                        $smsLog->regiNo    = $student->regiNo;
                        $smsLog->status    = $snd_msg;
                        $smsLog->save();
                }else{

                     $get_msg  = DB::table('ictcore_attendance')->first();
                    if($attendance_noti->type=='voice'){
                        if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){  
                           if (preg_match("~^0\d+$~", $student->fatherCellNo)) {
                                $to = preg_replace('/0/', '92', $student->fatherCellNo, 1);
                            }else {
                                $to =$student->fatherCellNo;  
                            }

                            $data= array(
                                'first_name'         => $student->firstName,
                                'last_name'          =>  $student->lastName,
                                'phone'              =>  $to,
                                'email'              => '',
                            );
                            $contact_id = $ict->ictcore_api('contacts','POST',$data );
                            if($student->status=="Absent"){
                             $program_id = 'program_id =>'.$get_msg->ictcore_program_id;
                             $msg=$get_msg->recording;
                            }else{
                               $program_id =  'program_id =>'.$get_msg->ictcore_program_id_late;
                                $msg=$get_msg->late_file;
                            }
                            $data = array(
                                        'title' => 'Attendance',
                                         $program_id,
                                        'account_id'     => 15,
                                        'contact_id'     => $contact_id,
                                        'origin'     => 1,
                                        'direction'     => 'outbound',
                                    );
                            $transmission_id = $ict->ictcore_api('transmissions','POST',$data );
                            $transmission_send = $ict->ictcore_api('transmissions/'.$transmission_id.'/send','POST',$data=array() );
                            if(!empty($transmission_send->error)){
                                $status =$transmission_send->error->message;
                            }else{
                                $status = "Completed";
                            }

                                 //echo "bhutta<pre>".$status;exit;
                            //$msg    = $recoding;
                            $smsLog = new SMSLog();
                            $smsLog->type      = "Attendancehello";
                            $smsLog->sender    = "ictcore voice";
                            $smsLog->message   = $msg;
                            $smsLog->recipient = $student->fatherCellNo;
                            $smsLog->regiNo    = $student->regiNo;
                            $smsLog->status    = $status;
                            $smsLog->save();
                        }
                    }else{
                        ///////Send sms ictcore
                          $this->info('Notssssification sended ttsuccessfully');    
                        echo 'EWEWEEW'.$sendictcoresms = $this->sendictcore($student,$attendance_noti);

                    }
                }
            }
        }
    }
}
}



    public function sendictcore($student,$attendance_noti){

        //return "testing";
            if($student->status=="Absent"){

                $get_msg  = DB::table('ictcore_attendance')->first();
                $name     = $student->firstName.' '.$student->lastName;
                if($attendance_noti->type=='sms'){
                    $msg      =  str_replace("<<parent>>",$student->fatherName,$get_msg->description);
                    $msg      =  str_replace("<<name>>",$name,$msg);
                }else{

                    $msg = $get_msg->telenor_file_id;     
                }
            }elseif($student->status=="Late" || $student->status=="late"){
                $get_msg  = DB::table('ictcore_attendance')->first();
                $name     = $student->firstName.' '.$student->lastName;
                if($attendance_noti->type=='sms'){
                    $msg      =  str_replace("<<parent>>",$student->fatherName,$get_msg->late_description);
                    $msg      =  str_replace("<<name>>",$name,$msg);
                }else{
                   $msg = $get_msg->telenor_file_id_late;         
                }
            }

                if (preg_match("~^0\d+$~", $student->fatherCellNo)) {
                    $to = preg_replace('/0/', '92', $student->fatherCellNo, 1);
                }else {
                    $to =$student->fatherCellNo;  
                }
                 if(strlen($to)==12){
                    //$snd_msg  = $ict->verification_number_telenor_sms($to,$msg,'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,$attendance_noti->type);
                    $snd_msg = sendmesssageictcore($student->firstName,$student->lastName,$to,$msg,'marks');

                    //$snd_msg =  $snd_msg->response;
                    print_r( $snd_msg);

                   }
                    $smsLog = new SMSLog();
                        $smsLog->type      = "Attendance";
                        $smsLog->sender    = "ictcore";
                        $smsLog->message   = $msg;
                        $smsLog->recipient = $student->fatherCellNo;
                        $smsLog->regiNo    = $student->regiNo;
                        $smsLog->status    = $snd_msg;
                        $smsLog->save();
    }


}
