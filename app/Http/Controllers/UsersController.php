<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\ICTCoreController;
use App\Models\Institute;
use App\Models\User;
use App\Models\VerifyCode;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Hash;

class UsersController extends BaseController
{

  // use AuthenticatesUsers;
  public function __construct()
  {
    /*$this->beforeFilter('csrf', array('on'=>'post'));
    $this->beforeFilter('auth', array('only'=>array('show','create','edit','update')));
    $this->beforeFilter('userAccess',array('only'=> array('show','create','edit','update','delete')));*/
    //5.5
    //$this->middleware('csrf', array('on'=>'post'));
    $this->middleware('auth', array('only' => array('show', 'create', 'edit', 'update')));
  }
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function postSignin(request $request)
  {

    $otp_check  = \Config::get('app.otp');

    //echo "fcf".$otp_check ;
    $institute = Institute::select('name')->first();

    if ($otp_check == "No") {
      if (\Auth::attempt(array('login' => $request->input('login'), 'password' => $request->input('password')))) {

        $login   = Auth::user()->group;
        /*if($login == "Admin"){
              $user_id = Auth::user()->id;
              $phone = Auth::user()->phone;
            \Auth::logout();
            $this->sendcode($user_id,$phone);
            return Redirect::to('/verify_code');

          }*/


        $name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $login = Auth::user()->group;
        Session::put('name', $name);
        Session::put('userRole', $login);

        if (!$institute) {
          if (Auth::user()->group != "Admin") {
            return Redirect::to('/')
              ->withInput($request->all())->with('error', 'Institute Information not setup yet!Please contact administrator.');
          } else {
            $institute = new Institute;
            $institute->name = "IctVission";
            \Session::put('inName', $institute->name);
            return Redirect::to('/institute')->with('error', 'Please provide institute information!');
          }
        } else {
          \Session::put('inName', $institute->name);
          return Redirect::to('/dashboard')->with('success', 'You are now logged in.');
        }
      } else {
        return Redirect::to('/')
          ->withInput($request->all())->with('error', 'Your username/password combination was incorrect');
      }
    } else {

      // $this->validateLogin($request);

      if (\Auth::attempt(array('login' => $request->input('login'), 'password' => $request->input('password')))) {
        //if ($user = app('auth')->getProvider()->retrieveByCredentials($request->only('email', 'password'))) {
        $user_id = Auth::id();
        $token = VerificationCode::where('user_id', $user_id)->where('ip_address', $request->ip());
        if ($token->count() == 0) {


          $token_create = VerificationCode::create(
            [

              'user_id' => $user_id,
              'status' => 1,
              'ip_address' => $request->ip()
            ]
          );         //$code = $token->first()->generateCode;exit;

          $ict     = new ICTCoreController();

          if (preg_match("~^0\d+$~", Auth()->user()->phone)) {
            $phone = preg_replace('/0/', '92', Auth()->user()->phone, 1);
          } else {
            $phone = Auth()->user()->phone;
          }
          $message = 'Your verification code is ' . $token_create->code;
          $data = array('numbers' => $phone, 'message' => $message);
          $snd_msg  = $ict->biz_sms($data);
          //exit;
          \Auth::logout();
          return Redirect::to('/verification_code?id=' . $token_create->id);
        }



        \Session::put('inName', $institute->name);
        return Redirect::to('/dashboard')->with('success', 'You are now logged in.');
      } else {

        return Redirect::to('/')
          ->withInput($request->all())->with('error', 'Your username/password combination was incorrect');
      }
    }
  }

  public function codeverify(Request $request)
  {
    $institute = Institute::select('name')->first();
    if (!$institute) {
      $institute = new Institute;
      $institute->name = "ictvission";
    }
    $id = $request->get('id');
    return view('verificationcode', compact('institute', 'id'));
  }
  public function code_check(Request $request)
  {

    $check = VerificationCode::find($request->id);

    if ($check->code == $request->code) {

      if (Auth::loginUsingId($check->user_id)) {
        //request()->session()->flush();
        $name  = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $login = Auth::user()->group;
        \Session::put('name', $name);
        \Session::put('userRole', $login);
        return redirect('/dashboard');
      }
    }

    return Redirect::to('/verification_code?id=' . $request->id)
      ->withInput($request->all())->withErrors('Your code was incorrect');
  }

  public function verify_code(Request $request)
  {
    $error = \Session::get('error');
    $institute = Institute::select('name')->first();
    if (!$institute) {
      $institute = new Institute;
      $institute->name = "IctVission";
    }
    return View('app.users.verify', compact('error', 'institute'));
  }

  public function sendcode($user_id, $phone)
  {

    $verified_code = hexdec(substr(uniqid(rand(), true), 5, 5));
    $verification_code = new VerifyCode;
    $verification_code->user_id = $user_id;
    $verification_code->code = $verified_code;
    $verification_code->save();

    /* $ict         = new ICTCoreController();
                    $contact = array(
                      'firstname' => 'admin',
                      'lastname' =>'',
                      'phone'     =>$phone,
                      'email'     => '',
                      );
                $msg = "verification code is ". $verified_code;
                 $ict_stting = DB::table('ict_settings')->first();
                 if($ict_stting->type=='ictcore'){
                $ict->verification_number($contact,$msg);*/
    $msg = "verification code is " . $verified_code;
    $send_msg_ictcore = sendmesssageictcore('admin', '', $phone, $msg, 'verified code');
  }

  public function verified(Request $request)
  {
    $verification_code = VerifyCode::first();

    if (!empty($verification_code) && $verification_code->code == $request->input('code')) {

      $user_id = $verification_code->user_id;
      VerifyCode::truncate();
      if (Auth::loginUsingId($user_id)) {

        $name = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $login = Auth::user()->group;
        \Session::put('name', $name);
        \Session::put('userRole', $login);
        $institute = Institute::select('name')->first();
        if (!$institute) {
          if (Auth::user()->group != "Admin") {
            return Redirect::to('/verify_code')
              ->withInput($request->all())->with('error', 'Institute Information not setup yet!Please contact administrator.');
          } else {
            $institute = new Institute;
            $institute->name = "IctVission";
            \Session::put('inName', $institute->name);
            return Redirect::to('/institute')->with('error', 'Please provide institute information!');
          }
        } else {
          \Session::put('inName', $institute->name);
          return Redirect::to('/dashboard')->with('success', 'You are now logged in.');
        }
      }
    } else {
      return Redirect::to('/verify_code')
        ->withInput($request->all())->with('error', 'Code Not Match please enter Correct Code');
    }
  }

  public function getLogout()
  {
    /*request()->session()->flush();
    \Auth::logout();*/

    if (request()->session()->pull('isAdmin', 0)) {
      $id = request()->session()->pull('adminID', 0);
      //$url = request()->session()->pull('surl','');
      //$id = request()->session()->pull('adminID', 0);
      if (Auth::loginUsingId($id)) {
        //request()->session()->flush();
        $name  = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $login = Auth::user()->group;
        \Session::put('name', $name);
        \Session::put('userRole', $login);
        return redirect('/dashboard');
      }
      return redirect('/dashboard');
    }
    request()->session()->flush();
    \Auth::logout();
    return redirect('/')->with('message', 'Your are now logged out!');
  }
  public function dologin($id, $usr_id)
  {
    $user = User::find($id);
    request()->session()->forget('isAdmin');
    request()->session()->forget('adminID');
    request()->session()->forget('surl');
    request()->session()->put('isAdmin', 1);
    request()->session()->put('adminID', $usr_id);

    // echo request()->root();
    //echo "<pre>rr".request()->session()->get('adminID')."tt";print_r($user);
    if (Auth::loginUsingId($id)) {

      $name  = Auth::user()->firstname . ' ' . Auth::user()->lastname;
      $login = Auth::user()->group;
      \Session::put('name', $name);
      \Session::put('userRole', $login);
      //echo "adeel";
      return redirect('/dashboard');
    }
  }

  public  function show()
  {
    //User::create(array('firstname'=>'Mr.','lastname'=>'kashif','login'=>'ictkashif','email' => 'kashif@ictinnovations.com','group'=>'Admin','desc'=>'admin Deatils Here',"password"=> Hash::make("123456")));
    $users = User::all();
    $user = array();
    //return View::Make('app.users',compact('users','user'));
    return View('app.users', compact('users', 'user'));
  }
  public  function create(Request $request)
  {
    $rules = [
      'firstname' => 'required',
      'lastname' => 'required',
      'email' => 'required|email',
      'group' => 'required',
      'desc' => 'required',
      'login' => 'required',
      'password' => 'required'

    ];
    $validator = \Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return Redirect::to('/users')->withInput($request->all())->withErrors($validator);
    } else {

      $uexits = User::select('*')->where('email', '=', $request->input('email'))->where('login', '=', $request->input('login'))->get();
      //  dd($uexits );
      //echo "<pre>";print_r($uexits);exit;
      if (count($uexits) > 0) {
        $errorMessages = new \Illuminate\Support\MessageBag;
        $errorMessages->add('deplicate', 'User all ready exists with this email or login');
        return Redirect::to('/users')->withInput($request->all())->withErrors($errorMessages);
      } {
        $user = new User;
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->login = $request->input('login');
        $user->desc = $request->input('desc');
        $user->email = $request->input('email');
        $user->group = $request->input('group');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return Redirect::to('/users')->with("success", "User Created Succesfully.");
      }
    }
  }
  public function edit($id)
  {
    $user = User::find($id);
    $users = User::all();
    //return View::Make('app.users',compact('users','user'));
    return View('app.users', compact('users', 'user'));
  }
  public  function update(Request $request)
  {
    $rules = [
      'firstname' => 'required',
      'lastname'  => 'required',
      'email'     => 'required|email',
      'group'     => 'required',
      'desc'      => 'required',
      'login'     => 'required',
      'password'  => 'required'

    ];
    $validator = \Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return Redirect::to('/usersedit/' . $request->input('id'))->withErrors($validator);
    } else {

      $uexits = User::select('*')->orwhere('email', '=', $request->input('email'))->first();
      if ($uexits->count() > 0) {

        if ($uexits->id != $request->input('id')) {
          $errorMessages = new \Illuminate\Support\MessageBag;
          $errorMessages->add('deplicate', 'User all ready exists with this email');
          return Redirect::to('/users')->withInput($request->all())->withErrors($errorMessages);
        } else {
          $user            = User::find($request->input('id'));
          $user->firstname = $request->input('firstname');
          $user->lastname  = $request->input('lastname');
          $user->login     = $request->input('login');
          $user->desc      = $request->input('desc');
          $user->email     = $request->input('email');
          $user->group     = $request->input('group');
          $user->password  = Hash::make($request->input('password'));
          $user->save();
          return Redirect::to('/users')->with("success", "User Updated Succesfully.");
        }
      } else {
        $user = User::find($request->input('id'));
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->login = $request->input('login');
        $user->desc = $request->input('desc');
        $user->email = $request->input('email');
        $user->group = $request->input('group');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        return Redirect::to('/users')->with("success", "User Updated Succesfully.");
      }
    }
  }

  public function delete($id)
  {
    $user = User::find($id);
    $user->delete();
    return Redirect::to('/users')->with("success", "User Deleted Succesfully.");
  }

  public function generateCode($codeLength = 4)
  {
    $min = pow(10, $codeLength);
    $max = $min * 10 - 1;
    $code = mt_rand($min, $max);

    return $code;
  }

  public function session(Request $request)
  {

    // $request->session()->flash('success', 'This is a success message');
    // return view('alert');

    return Redirect::to('/')->with('message', 'This is a success message222');
    return view('alert')->with('message', 'This is a success message 2222');
  }
}
