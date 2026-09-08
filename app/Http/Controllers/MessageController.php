<?php
namespace App\Http\Controllers;
use DB;
use App\Models\Level;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use App\Models\Ictcore_integration;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ICTCoreController;

class MessageController extends BaseController {

	public function __construct() 
	{
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('delete')));*/
		
	      $this->middleware('auth');
          $this->middleware('auth',array('only'=> array('delete')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		 $classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		 $messages = DB::table('message')
				    ->select(DB::raw('message.id,message.name,message.description,message.recording'))
				    ->get();
		return View('app.messageCreate',compact('classes','messages'));
		//echo "this is section controller";
	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(Request $request)
	{    
		if($request->input('role')=='student'){
			$rules=[
			'role' => 'required',
			'message' => 'required',
			'mess_name' => 'required',
			'class'   => 'required',
			'section' => 'required'
			];
        }
        if($request->input('role')=='teacher'){
			$rules=[
			'role' => 'required',
			'message' => 'required',
			'mess_name' => 'required',
			//'class'   => 'required',
			//'section' => 'required'
			];
        }
        if($request->input('role')=='testing' && $request->input('type')!='sms'){
			$rules=[
			'role'      => 'required',
			'message'   => 'required',
			'mess_name' => 'required',
			'message_file'      =>'required|mimes:wav'
			];
        }else{
        	$rules=[
        	'role' => 'required',
			'message' => 'required',
			'mess_name' => 'required',
			//'class'   => 'required',
			//'section' => 'required'
			];
        }
			$validator = \Validator::make($request->all(), $rules);
			if ($validator->fails())
			{
			  return Redirect::to('/message')->withErrors($validator);
			}else {
                 //echo "<pre>";echo $request->input('role');exit;
                   $file_id='';
                   $msg_type=$request->input('stpye');
                 //echo "<pre>";print_r($request->all());exit;
                  /*   $section = $request->input('section');
							$class = $request->input('class');
							$student=	DB::table('Student')
							->select('*')
							->where('isActive','Yes')
							->whereIn('section1', $section)
							->where('class', $class)
							->get();

							echo "<pre>";print_r($student->toArray());exit; */
                  //$phone = explode(',',$request->input('phone_number'));
			      //  echo "<pre>";print_r($phone);exit;
                  echo $type = $request->input('type');
                  $ictcore_integration = Ictcore_integration::select("*")->where('type',$type)->first();
                  $ict  = new ICTCoreController();
					if($request->input('message')=='other'){

						$drctry = storage_path('app/public/messages/');
					    $fileName = 'othernoti_'.time().'.'.Input::file('message_file')->getClientOriginalExtension();
                        Input::file('message_file')->move($drctry ,$fileName);
						sleep(2);
                        echo exec('sox '.$drctry.'/'.$fileName .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'/'.'other.wav');
						$name_ab          =  $drctry .'other.wav';
						$finfo_ab         =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype_ab      =  $finfo_ab->file($name_ab);
						$cfile_ab            =  curl_file_create($name_ab, $mimetype_ab, basename($name_ab));
						$data             = array('name'=>time(),'audio_file'=> $cfile_ab);
                       if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
                        	$file_id     = $ict->verification_number_telenor_voice($data,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
					}else{
						$data_abs = array(
							'name' => $request->input('title_abent'),
							'description' => $request->input('description_absent'),
							);
				          $recording_id  =  $ict->ictcore_api('messages/recordings','POST',$data_abs );
						if(!empty($recording_id->error)){
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
						}
                          $result        =  $ict->ictcore_api('messages/recordings/'.$recording_id.'/media','PUT',array( $cfile_ab));
				          $recording_id  =  $result ;
				          //
							if(!empty($recording_id->error)){
								return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
							}
							if(!is_array($recording_id )){
								$data = array(
								'name' => $request->input('title'),
								'recording_id' => $recording_id,
								);
								$program_id = $ict->ictcore_api('programs/voicemessage','POST',$data );
								if(!empty($program_id->error)){
									return Redirect::to('/message')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
								}
								if(!is_array( $program_id )){
									$program_id = $program_id;
								}else{
									return Redirect::to('/message')->withErrors("ERROR: Program not Created" );
								}
							}else{
							return Redirect::to('/message')->withErrors("ERROR: Recording not Created" );               
							}

							  echo "<pre>";print_r( $recording_id);
						 
					}
				}
					
					
					//echo "<pre>";print_r($ictcore_integration);

					//exit;
					if(!empty($ictcore_integration)   && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){

	                  
	                  $role = $request->input('role');
	                  $mess_name = $request->input('mess_name');
					  $remove_spaces_m =  str_replace(" ","_",$mess_name );
                       
						if($type=='voice' && $request->input('message')!='other' ){
							$message = Message::find($request->input('message'));
							$program_id =  $message->ictcore_program_id;
							$file_id =  $message->telenor_file_id;
						}elseif($type=='sms' || $type=='Sms' || $type=='SMS'){
							$data = array(
								'name' => $request->input('mess_name'),
								'data' => $request->input('message'),
								'type' => 'utf-8',
								'description' =>'',
							);
							if($ictcore_integration->method == 'telenor'){
                             echo "adeel";
							}else{
							echo "adeel";
							$text_id  =  $ict->ictcore_api('messages/texts','POST',$data );
							$data     = array(
								'name' => $request->input('mess_name'),
								'text_id' =>$text_id,
							);
							 $program_id  =  $ict->ictcore_api('programs/sendsms','POST',$data );
						}
						}
						if($ictcore_integration->method == 'telenor'){
                            if($msg_type!='quick'){
                            	$group_id = $ict->telenor_apis('group','','','','','');
                            }
						}else{
							if($msg_type!='quick'){
								$data = array(
											'name' => $remove_spaces_m,
											'description' => $mess_name,
										);
				                $group_id= $ict->ictcore_api('groups','POST',$data );
		                    }
                        }
						if($role =='student' || $role =='parent' || $role =='all_student'){

							$section = $request->input('section');
							$class = $request->input('class');
							$student=	DB::table('Student')
							->select('*')
							->where('isActive','Yes');
							if($request->input('role')!='all_student'){
							    $student=$student->whereIn('section', $section)
							         ->where('class', $class);
						    }
							$student=$student->get();

							//echo "<pre>";print_r($student->toArray());
							//exit;
							foreach($student as $std){
								if (preg_match("~^0\d+$~", $std->fatherCellNo)) {
                                	$to = preg_replace('/0/', '92', $std->fatherCellNo, 1);
	                            }else {
	                                $to =$std->fatherCellNo;  
	                            }
                                
								$data = array(
								'first_name' => $std->firstName,
								'last_name' => $std->lastName,
								'phone'     => $to,
								'email'     => '',
								);
                                 
                                if($ictcore_integration->method == 'telenor'){
                                    if($msg_type!='quick'){
                                		$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$to,'','','');
                                    }else{
                                        $snd_msg  = $ict->verification_number_telenor_sms($to,$request->input('message'),'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,$type);

                                    }
                                }else{
	                                    if($msg_type!='quick'){
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											$group      = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
										}else{
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											//$program_id = 'program_id =>'.$get_msg->ictcore_program_id;

											$data = array(
											'title' => 'Attendance',
											'program_id' =>$program_id,
											'account_id'     => 1,
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
										}
									}
							}
						}else if($role =='teacher'){
							$teacher=	DB::table('teacher')
							->select('*')
							->get();
							foreach($teacher as $techrd){
								if (preg_match("~^0\d+$~", $techrd->phone)) {
                                	$to = preg_replace('/0/', '92', $techrd->phone, 1);
	                            }else {
	                                $to =$techrd->phone;  
	                            }

								$data = array(
								'first_name' => $techrd->firstName,
								'last_name'  => $techrd->lastName,
								'phone'      => $to,
								'email'      => $techrd->email
								);
                                 if($ictcore_integration->method == 'telenor'){
                                  
                               // $group_contact_id = $ict->telenor_apis('add_contact',$group_id,$std->fatherCellNo,'','','');
                                   if($msg_type!='quick'){
                                		$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$to,'','','');
                                    }else{
                                        $snd_msg  = $ict->verification_number_telenor_sms($to,$request->input('message'),'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,$type);
                                    }
                                }else{
								//$contact_id = $ict->ictcore_api('contacts','POST',$data );
								//$group      = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
                                    if($msg_type!='quick'){
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											$group      = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
										}else{
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											//$program_id = 'program_id =>'.$get_msg->ictcore_program_id;

											$data = array(
											'title' => 'Attendance',
											$program_id,
											'account_id'     => 1,
											'contact_id'     => $contact_id,
											'origin'     => 1,
											'direction'     => 'outbound',
											);
											$transmission_id = $ict->ictcore_api('transmissions','POST',$data );
											
//echo "<pre>";print_r($transmission_id);exit;
											$transmission_send = $ict->ictcore_api('transmissions/'.$transmission_id.'/send','POST',$data=array() );
											if(!empty($transmission_send->error)){
											$status =$transmission_send->error->message;
											}else{
											$status = "Completed";
											}
										}
							     
							    }
							}
						}else{
                          $phone = explode(',',$request->input('phone_number'));
                          foreach($phone as $number){
                            if (preg_match("~^0\d+$~", $number)) {
                                	$to = preg_replace('/0/', '92', $number, 1);
	                            }else {
	                                $to =$number;  
	                            }
                            if($ictcore_integration->method == 'telenor'){
                                //$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$number,'','','');
                                if($msg_type!='quick'){
                                		$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$to,'','','');
                                    }else{
                                        $snd_msg  = $ict->verification_number_telenor_sms($to,$request->input('message'),'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,$type);
                                    }
                                }else{
                                     $data = array(
										'first_name' => '',
										'last_name'  =>'',
										'phone'      =>$to,
										'email'      =>''
								);
									//$contact_id = $ict->ictcore_api('contacts','POST',$data );
									//$group      = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
							        if($msg_type!='quick'){
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											$group      = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
										}else{
											$contact_id = $ict->ictcore_api('contacts','POST',$data );
											
											//$program_id = 'program_id =>'.$get_msg->ictcore_program_id;

											$data = array(
											'title' => 'Attendance',
											//$program_id,
											'program_id' =>$program_id,
											'account_id'     => 1,
											'contact_id'     => $contact_id,
											'origin'     => 1,
											'direction'     => 'outbound',
											);

											$transmission_id   = $ict->ictcore_api('transmissions','POST',$data );
											//echo "<pre>";print_r($transmission_id);
											///exit;
											$transmission_send = $ict->ictcore_api('transmissions/'.$transmission_id.'/send','POST',$data=array() );
											if(!empty($transmission_send->error)){
											$status =$transmission_send->error->message;
											}else{
											$status = "Completed";
											}
										}
							    }
							}
						}
						if($msg_type!='quick'){
							if($ictcore_integration->method == 'telenor'){
	                        echo  $campaign    = $ict->telenor_apis('campaign_create',$group_id,'',$request->input('message'),$file_id,$type);
	                               // echo $campaign;
	                              // $this->info('Notification sended successfully'.$campaign);
	                             // echo "<pre>";print_r($campaign);
	                            // exit;
	                            $send_campaign = $ict->telenor_apis('send_msg','','','','',$campaign);
	                             // echo "<pre>";print_r($send_campaign);
	                            // exit;
	                        }else{
							$data = array(
							'program_id' => $program_id,
							'group_id' => $group_id,
							'delay' => '',
							'try_allowed' => '',
							'account_id' => 1,
							);
							$campaign_id    = $ict->ictcore_api('campaigns','POST',$data );
	                        $campaign_start = $ict->ictcore_api('campaigns/'.$campaign_id.'/start','PUT',$data=array() );

							}
					    } 


						//exit;
						return Redirect::to('/message')->with("success", "campaign Created Succesfully.");
					}else{
						return Redirect::to('/message')->withErrors("Please Add ictcore integration in Setting Menu");
					}
		}
		//}
	}
	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function show()
	{
		//$Classes = ClassModel::orderby('code','asc')->get();
		$levels = DB::table('level')
		->select(DB::raw('level.id,level.name,level.description'))
		->get();
		//dd($sections);
		//return View::Make('app.classList',compact('Classes'));
		return View('app.levelList',compact('levels'));
	}



	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		$level = Level::find($id);
		//return View::Make('app.classEdit',compact('class'));
		return View('app.levelEdit',compact('level'));
	}


	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update(Request $request)
	{
		$rules=[
			'name' => 'required',
			'description' => 'required'
		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/level/edit/'.$request->input('id'))->withErrors($validator);
		}
		else {
			$section = Level::find($request->input('id'));
			$section->name= $request->input('name');

			$section->description=$request->input('description');
			$section->save();
			return Redirect::to('/level/list')->with("success","Level Updated Succesfully.");

		}
	}


	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function delete($id)
	{
		$class = Level::find($id);
		$class->delete();
		return Redirect::to('/level/list')->with("success","Level Deleted Succesfully.");
	}
}
