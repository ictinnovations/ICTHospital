<?php
namespace App\Http\Controllers;
use DB;
use Storage;
use App\Models\Message;
use App\Models\ClassModel;
use App\Models\Ictcore_integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ICTCoreController;

class TemplateController extends BaseController {

	public function __construct() {
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
		$message = Message::first();
		/*if(!empty($message)){
			return Redirect::to('/message/edit/'.$message ->id);
		}*/
	         
		return View('app.templateCreate');
	}
	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(Request $request)
	{
		$rules=[
			'title' => 'required',
			'description' => 'required'
			//'message' => 'required|mimes:audio/wav',
			//'message' => 'required|mimes:wav',

		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails())
		{
			if($request->input('title')=='mark_notification'){
				return Redirect::to('/template/creates')->withErrors($validator);
			}
			return Redirect::to('/template/create')->withErrors($validator);
		}
		else {

            // echo "<pre>";print_r(Input::file('message'));exit;

			$sname = $request->input('title');
			$sexists=message::select('*')->where('name','=',$sname)->get();
			if(count($sexists)>0){

				$errorMessages = new \Illuminate\Support\MessageBag;
				$errorMessages->add('deplicate', 'Title all ready exists!!');
				if($request->input('title')=='mark_notification'){
					return Redirect::to('/template/creates')->withErrors($errorMessages);
				}
				return Redirect::to('/template/create')->withErrors($errorMessages);
			}
			else {


				$ictcore_message = new Message;
				$ictcore_message->name = $request->input('title');
				$ictcore_message->description = $request->input('description');
			    $ictcore_message->recording ='';
			    $ictcore_message->ictcore_recording_id ='';
                $ictcore_message->ictcore_program_id  ='';
                $ictcore_message->telenor_file_id  ='';
				$ictcore_message->save();
				if($request->input('title')=='mark_notification'){
					//return Redirect::to('/template/creates')->withErrors($errorMessages);
					return Redirect::to('/template/list/')->with("success", "Message Created Succesfully.");
				}
				return Redirect::to('/template/list/')->with("success", "Message Created Succesfully.");

               /* $remove_spaces =  str_replace(" ","_",$request->input('title'));

				$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();

				$class = new Message;
				$class->name = $request->input('title');
				$class->description = $request->input('description');
			    $class->recording =$fileName;

				$class->save();
				Input::file('message')->move(base_path() .'/public/recording',$fileName);

				return Redirect::to('/template/create')->with("success", "Message Created Succesfully.");*/

               /*$ictcore_integration = Ictcore_integration::select("*")->first();
                   
		    	if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){
				$ictcore_api  = new ICTCoreController();
				$sname = $request->input('title');
                $remove_spaces =  str_replace(" ","_",$request->input('title'));
				$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();

				 $drctry = storage_path('app/public/messages/');

                Input::file('message')->move($drctry,$fileName);
                sleep(3);
               echo 	$change_wav_mono =  exec('sox '.$drctry.$fileName .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'mono.wav');
               //echo $drctry.$fileName;
                //exit;
                 $name              =   $drctry .$fileName;
                 $telenor_name      =   $drctry .'mono.wav';

                 $finfo             =  new \finfo(FILEINFO_MIME_TYPE);
                 $mimetype          =  $finfo->file($name);
                 $mimetypet         =  $finfo->file($telenor_name);
                 $cfile             =  curl_file_create($telenor_name, $mimetypet, basename($telenor_name));
                 $data              =     array( $cfile);
                 $telenor_api_data  = array('name'=>time(),'audio_file'=> $cfile);
              
                $ictcore_integration =	DB::table('ictcore_integration')->select('*')->where('type','voice')->first();
               if($ictcore_integration->method=="telenor"){

                 $upload_audio  = $ictcore_api->verification_number_telenor_voice($telenor_api_data,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                  
              

			     unlink($drctry .'mono.wav');

                return Redirect::to('/template/create')->with("success", "Message Created Succesfully.");


              }else{
                $data = array(
                             'name' => $request->input('title'),
				             'description' => $request->input('description'),
							 );

                 $recording_id  =  $ictcore_api->ictcore_api('messages/recordings','POST',$data );
                 
				 $result        =  $ictcore_api->ictcore_api('messages/recordings/'.$recording_id.'/media','PUT',$data );
                 $recording_id  =  $result ;
                if(!is_array($recording_id )){

                  $data = array(
                             'name' => $request->input('title'),
				             'recording_id' => $recording_id,
							 );
                 $program_id = $ictcore_api->ictcore_api('programs/voicemessage','POST',$data );
                 if(!is_array( $program_id )){
                  $program_id = $program_id;
                 }else{
                 	return Redirect::to('/template/create')->withErrors("ERROR: Program not Created" );
                 }
                }else{
                     return Redirect::to('/template/create')->withErrors("ERROR: Recording not Created" );               
                }
            }
				$ictcore_message = new Message;
				$ictcore_message->name = $request->input('title');
				$ictcore_message->description = $request->input('description');
			    $ictcore_message->recording =$fileName;
			    $ictcore_message->ictcore_recording_id =$recording_id;
                $ictcore_message->ictcore_program_id  =$program_id;
                $ictcore_message->telenor_file_id  ='';
				$ictcore_message->save();
				return Redirect::to('/template/create')->with("success", "Message Created Succesfully.");
			

          }else{
          	  return Redirect::to('/template/create')->withErrors("Please Add ictcore integration in Setting Menu");
          }*/
		}

		}

	}


	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function show()
	{
		//$Classes = ClassModel::orderby('code','asc')->get();
		$messages = DB::table('message')
		->select(DB::raw('message.id,message.name,message.description,message.recording'))
		->get();

   //$path = Storage::disk('public')->getDriver();
   //print_r($path);

		////echo Storage::get('app/public');
	//	exit;
		//dd($sections);
		//return View::Make('app.classList',compact('Classes'));
		return View('app.messageList',compact('messages'));
	}



	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		$message = Message::find($id);
		//return View::Make('app.classEdit',compact('class'));
		return View('app.messageEdit',compact('message'));
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
			'title' => 'required',
		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails())
		{
			if($request->input('title')=='mark_notification'){
					return Redirect::to('/template/creates')->withErrors($errorMessages);
					return Redirect::to('/message/edit/'.$ictcore_message->id)->with("success", "Message Created Succesfully.");

				}


			return Redirect::to('/message/edit/'.$request->input('id'))->withErrors($validator);
		}
		else {


        /*if(Input::hasFile('message'))
		{

			
   
			if(substr(Input::file('message')->getMimeType(), 0, 5) != 'audio')
			{
				$messages = $validator->errors();
				$messages->add('Notvalid!', 'Audio must be a audio wav!');
				return Redirect::to('/message/edit/'.$request->input('id'))->withErrors($messages);
			}
			else {

				$remove_spaces =  str_replace(" ","_",$request->input('title'));
				$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();

				//$student->photo = $fileName;
				$drctry = storage_path('app/public/messages/');
				Input::file('message')->move($drctry,$fileName);
			}

		}
		else {
			   $fileName = $request->input('recording');

		}*/





			$message = Message::find($request->input('id'));
			$message->name= $request->input('title');

			$message->description=$request->input('description');
			//$message->recording=$fileName;

			$message->save();
			return Redirect::to('/message/edit/'.$request->input('id'))->with("success","Message Updated Succesfully.");

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

		$message = Message::find($id);
		$message->delete();
		$drctry = storage_path('app/public/messages/');
		unlink($drctry.$message->recording);
		return Redirect::to('/template/list')->with("success","Message Deleted Succesfully.");
	}

}
