<?php
namespace App\Http\Controllers;
use DB;
use Auth;
use Hash;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class SettingsController extends BaseController 
{
	public function __construct() 
	{
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth', array('only'=>array('save','index')));*/
		$this->middleware('auth');
		$this->middleware('auth', array('only'=>array('save','index')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$set= User::select("*")->where('id','=',Auth::id())->first();

		//return View::Make('app.Settings',compact('set'));
		return View('app.Settings',compact('set'));
	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function save(Request $request)
	{
		$rules=[
			'firstname' => 'required',
			'lastname' => 'required',
			'login' => 'required',
			//'email' => 'required',
			'desc' => 'required',
			'cpassword' => 'required',
			'npassword' => 'required',
			'cnpassword' => 'required'


		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/settings')->withinput($request->all())->withErrors($validator);
		}
		else {
			if ($request->input('npassword') == $request->input('cnpassword')) {

				// $u = User::select('*')->where('password',Hash::make($request->input('cpassword')))->first();
				//return Hash::make($request->input('cpassword'));
				//if(count($u)>0) {
				$user = User::find($request->input('id'));
				$user->firstname = $request->input('firstname');
				$user->lastname = $request->input('lastname');
				//  $user->login = $request->input('login');
				//$user->desc = $request->input('desc');
				// $user->email = $request->input('email');
				$user->password = Hash::make($request->input('npassword'));
				$user->save();

				return Redirect::to('/settings')->with('success', 'Settings is changed please relogin the site.');
					/*}
					else
					{
					$errorMessages = new Illuminate\Support\MessageBag;
					$errorMessages->add('notmatch', 'Current Password did not match!');
					return Redirect::to('/settings')->withErrors($errorMessages);
				}*/
			}else{
				$errorMessages = new Illuminate\Support\MessageBag;
				$errorMessages->add('notmatch', 'New Password and confirm password did not match!');
				return Redirect::to('/settings')->withErrors($errorMessages);
			}
		}
	}

	public function get_schedule()
	{

		 $schedule = Schedule::select('date','time')->first();
		if(is_null($schedule)){
			$schedule=new Schedule;
			$schedule->date = "";
			$schedule->time = "";
		
		}
		$datee=date('F');
		$year= date('Y');
		return View('app.schedulesetting',compact('schedule','datee','year'));
	}

	public function post_schedule(Request $request)
	{
		$rules=[
		'time' => 'required',
		'date' => 'required',
		];
		$validator = \Validator::make($request->all(), $rules);
		if ($validator->fails()){
			return Redirect::to('/schedule')->withErrors($validator);
		}
		else{
			   $time =  date("H:i", strtotime($request->input('time')));
			   
				DB::table("cronschedule")->delete();
				$schedule=new Schedule;
				$schedule->date = $request->input('date');
				$schedule->time = $time;
				$schedule->save();
				return Redirect::to('/schedule')->with("success", "Schedule Created Succesfully.");
			}
	}


}
