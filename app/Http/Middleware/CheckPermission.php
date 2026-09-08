<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Auth;
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$permission_name)
    {
        //return $next($request);
         //here you have to get logged in user role
        $role = strtolower(Auth::user()->group);
        //$role = 'admin';
         // so now check permission
         $permission = DB::table('permission')->where('permission_group', strtolower($role))->where('permission_name',$permission_name)->where('permission_type','yes')->first();
        
        //echo "<pre>";print_r($permission);exit;
            if($permission){
                 return $next($request);
              //if Permission not assigned for this user  show what you need
            }
            return redirect('user-have-no-permission');
    }
}
