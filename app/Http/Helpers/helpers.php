<?php
use App\Models\Subject;
use App\Models\FeeSetup;
use App\Models\FeeCol;
use App\Http\Controllers\ICTCoreController;
use Carbon\Carbon;
//use Storage;
class emtysession{

}
if (! function_exists('FixData')) {
	
}
if(! function_exists('accounting_check')) {
	function accounting_check()
	{
		if(Storage::disk('local')->exists('/public/accounting.txt')){
          $ac = Storage::get('/public/accounting.txt');
          $ac_data = explode('<br>',$ac );

			//echo "<pre>";print_r($data);
			$accounting = $ac_data[0]; 
		}else{
	      $accounting ='no';
		}
		return $accounting;
	}
}
if(! function_exists('php_curl')) {
if (! function_exists('getsubjecclass')) {
}

}

if (! function_exists('get_current_session')) {

}
if (! function_exists('count_student')) {

}

if(! function_exists('family_check')) {
}
if(! function_exists('branchesapi')) {
}
if(! function_exists('gettyperesult')){
}

if(! function_exists('sendmesssageictcore')){

	function sendmesssageictcore($first_name,$last_name,$to,$message,$m_name)
	{
		//exit;
		$ict  = new ICTCoreController();
		$data = array(
					'first_name' =>$first_name,
					'last_name'  =>$last_name,
					'phone'      =>$to,
					'email'      =>''
				);
		$contact_id = $ict->ictcore_api('contacts','POST',$data );
		
		$data = array(
						'name' => $m_name,
						'data' => $message,
						'type' => 'utf-8',
						'description' =>'',
				);
		$text_id  =  $ict->ictcore_api('messages/texts','POST',$data );
		$data     = array(
						'name' =>$m_name,
						'text_id' =>$text_id,
					);
		$program_id  =  $ict->ictcore_api('programs/sendsms','POST',$data );

		$data = array(
						'title' => 'Attendance',
						//$program_id,
						'program_id' =>$program_id,
						'account_id'     => 15,
						'contact_id'     => $contact_id,
						'origin'     => 1,
						'direction'     => 'outbound',
					);
		$transmission_id   = $ict->ictcore_api('transmissions','POST',$data );
		$transmission_send = $ict->ictcore_api('transmissions/'.$transmission_id.'/send','POST',$data=array() );
	  	if(!is_array($transmission_send) || !is_object($transmission_send)){
	  		$transmission_send = 'sended';
	  	}
	  	return $transmission_send;
	}

}

if (! function_exists('Voucharcheck')) {
}

if (! function_exists('gfee_setup')) {
}
if (! function_exists('gsection_name')) {
}
if (! function_exists('gclass_name')) {
}

if (! function_exists('teacher_details_f')) {
}
if (! function_exists('getdatainvoice')) {
}
if (! function_exists('getrefralindfo')) {
}
if (! function_exists('getinstitueinfo')) {
}

