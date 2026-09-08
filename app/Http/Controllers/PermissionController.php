<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use DB;
class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions = Permission::count();
        if($permissions>0){
            $permissions = Permission::get();
        }else{
            $permissions =array(); 
        }
        $admin='';
        $teacherd='';
        $studentss='';
        $accountant='';
        if($request->input('admin')){
          $admin="yes";
        }
        if($request->input('teacher')){
          $teacherd="yes";
        }
        if($request->input('student')){
          $studentss="yes";
        }
        if($request->input('accountant')){
          $accountant="yes";
        }
       return view('app/permission',compact('permissions','admin','teacherd','studentss','accountant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //echo "<pre>";print_r();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $permission_fields = array(
          'Student View',
          'Student Add',
          'Student Update',
          'Student Delete',
          'Student Info',
          'Student Student Portal Access',
          'Student Student Bulk Add',
          'Add Student Attendance',
          'View Student Attendance',
          'View Student Monthly Reports',
          'Family',
          'Add Marks',
          'View Marks',
          'Delete Marks',
          'Generate Result',
          'Search Result',
          'promote Student',
          'Add Fess',
          'View Fess',
          'Update Fess',
          'Delete Fess',
          'View Fess Report',
          'View Result Reports',
          'View Attendance Reports',
          'View Sms/voice log Reports',
          //'View Student Monthly Reports',
          'Class View',
          'Class Add',
          'Class update',
          'Class delete',
          'Section View',
          'Section add',
          'Section update',
          'Section Delete',
          'Section Time Table',
          'Teacher View',
          'Teacher Add',
          'Teacher Bulk Add',
          'Teacher update',
          'Teacher delete',
          'Teacher timetable add',
          'Teacher timetable view',
          'Teacher Portal Access',
          'Send Sms/Voice',
          'Setting GPA Rule view',
          'GPA Rule add',
          'GPA Rule update',
          'GPA Rule delete',
          'GPA Rule View',
          'holidays add',
          'holidays view',
          'holidays delete',
          'Class off view',
          'Class off add',
          'Class off delete',
          'Institute information add',
          'Grade system (auto,manual)',
          'Subject View',
          'Subject Add',
          'Subject update',
          'Subject delete',
          'Exam View',
          'Exam Add',
          'Exam update',
          'Exam delete',
          'Gradesheet View',
          'Gradesheet Print',
          'Send Notification',
          'Paper View',
          'Paper Add',
          'Paper update',
          'Paper delete',
          'Accounting',
        );
   /* DB::table("permission")->delete();*/
      DB::table('permission')->truncate();
      foreach($permission_fields as $field){
        $permission_save = new Permission;
        $field_name = str_replace(" ","_",strtolower($field));
        $admin =$request->input('admin');
            if(!empty($request->input('admin')) && in_array($field_name, array_keys($request->input('admin')))){
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'admin'     ;
                $permission_save->permission_type  =  'yes'       ;  
                $permission_save->save();
            }else{
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'admin'     ;
                $permission_save->permission_type  =  'no'       ;  
                $permission_save->save();
            }
      }
      foreach($permission_fields as $field){
        $permission_save = new Permission;
        $field_name = str_replace(" ","_",strtolower($field));
        
            if(!empty($request->input('student')) && in_array($field_name, array_keys($request->input('student')))){
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'student'     ;
                $permission_save->permission_type  =  'yes'       ;  
                $permission_save->save();
            }else{
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'student'     ;
                $permission_save->permission_type  =  'no'       ;  
                $permission_save->save();
            }
      }
      foreach($permission_fields as $field){
        $permission_save = new Permission;
        $field_name = str_replace(" ","_",strtolower($field));
            if(!empty($request->input('teacher')) && in_array($field_name, array_keys($request->input('teacher')))){
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'teacher'     ;
                $permission_save->permission_type  =  'yes'       ;  
                $permission_save->save();
            }else{
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'teacher'     ;
                $permission_save->permission_type  =  'no'       ;  
                $permission_save->save();
            }
      }

      foreach($permission_fields as $field){
        $permission_save = new Permission;
        $field_name = str_replace(" ","_",strtolower($field));
            if(!empty($request->input('accutant')) && in_array($field_name, array_keys($request->input('accutant')))){
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'accountant'     ;
                $permission_save->permission_type  =  'yes'       ;  
                $permission_save->save();
            }else{
                $permission_save->permission_name  =  $field_name ;
                $permission_save->permission_group =  'accountant'     ;
                $permission_save->permission_type  =  'no'       ;  
                $permission_save->save();
            }
      }
                return Redirect::to('/permission')->with("success","Permission Save Succesfully.");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
    }

    public function get_permission_by_role()
    {
         $user = Auth::user();
        $permissions = Permission::count();
        if($permissions>0){
            $permissions = Permission::where('permission_group',strtolower($user->group))->where('permission_type','yes')->get();
        }else{
            $permissions =array(); 
        }
       return $permissions ;
    }
}
