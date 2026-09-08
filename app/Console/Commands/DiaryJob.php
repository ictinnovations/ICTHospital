<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ICTCoreController;

use App\Student;
use App\Diary;
use DB;
use Carbon\Carbon;
use Storage;
use App\Ictcore_integration;
use App\Ictcore_attendance;
use App\Ictcore_fees;
use App\SectionModel;
use App\ClassModel;
use App\Notification;
use App\SMSLog;
class DiaryJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DiaryJob:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notification to student';

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
        if(Storage::disk('local')->exists('/public/cronsettingdiary.txt')){
            $section_ids    = DB::table('diaries')->where('diary_date',Carbon::today()->toDateString())->pluck('section');
            $get_students   = Student::where('session',get_current_session()->id)
                                    ->where('isActive','Yes')
                                    ->whereIn('section',$section_ids)
                                    ->get();
                    if(!empty($get_students)){
                            $check = array();
                        foreach($get_students as $student){
                            //$this->info($student->fatherCellNo.'diary'.$diary->diary);
                            //break;
                            $name = $student->firstName.$student->lastName;
                            $check[$student->firstName.$student->lastName]=$this->send_sms($student->class,$student->section,$student->fatherCellNo,$name);
                            break;
                        }
                        echo "<pre>";print_r($check);
                    }


        }
    }
       


    public function send_sms($class,$section,$phone,$name)
    {

        $get_diaries   = Diary::where('diary_date',Carbon::today()->toDateString())
                                ->where('section',$section)
                                ->where('class',$class)
                                ->get();

           //echo "<pre>";print_r($get_diaries->toArray());
            if(!empty($get_diaries)){
                $output = Carbon::today()->toDateString()."\n".$name."\n";
                foreach($get_diaries as $diary){
                 $subject = DB::table('Subject')->where('id',$diary->subject)->first();
                                               // $this->info($student->fatherCellNo.'diary'.$diary->diary);
                 $output .= ' subject '.$subject->name."\n".' diary: '. $diary->diary."\n";


                }

                //return $output;
                ///

                $body    = $output;
                $ict     = new ICTCoreController();
                $i       = 0;
                $attendance_noti     = DB::table('notification_type')->where('notification','fess')->first();
                $ictcore_fees        = Ictcore_fees::select("*")->first();
                $ictcore_integration = Ictcore_integration::select("*")->where('type','sms');
                if($ictcore_integration->count()>0){
                    $ictcore_integration = $ictcore_integration->first();
                }else{
                    return 404;
                }
                $contacts = array();
                $contacts1 = array();
                $i=0;
                if (preg_match("~^0\d+$~", $phone)) {
                    $to = preg_replace('/0/', '92', $phone, 1);
                }else {
                    $to =$phone;  
                }
                if(strlen(trim($to))==12){
                    $contacts = $to;
                }
                
                $msg = $body ;
                if($ictcore_integration->method!='ictcore'){
                    $snd_msg  = $ict->verification_number_telenor_sms($to,$msg,'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,'sms');
                }else{
                   $send_msg_ictcore = sendmesssageictcore('','',$to,$msg,'dairy'); 
                }
                return 200;

                //
            }

        
    }
}
