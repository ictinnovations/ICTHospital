<?php

namespace App\Http\Middleware;

use Closure;
use Auth ;
class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       if (Auth::user() &&  Auth::user()->group == 'Admin' && Auth::user()->login=='ictkashif') {
    
            return $next($request);
     }

    return redirect('page-not-found');
    }
}
