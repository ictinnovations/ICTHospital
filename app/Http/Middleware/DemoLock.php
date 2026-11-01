<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Public demo mode. Logins listed in DEMO_LOCKED_LOGINS (comma separated) can't have their
// password, login or role changed, and can't be deleted, by anyone, the admin included. A shared
// demo then keeps working for the next visitor. Leave it unset on a real install.
class DemoLock
{
    public function handle(Request $request, Closure $next): Response
    {
        $locked = array_filter(array_map('trim', explode(',', (string) config('app.demo_locked_logins'))));
        if (!$locked || !Auth::check()) {
            return $next($request);
        }

        $target = null;
        if ($request->isMethod('post') && $request->is('settings')) {
            $target = Auth::user();
        } elseif ($request->isMethod('post') && $request->is('userupdate')) {
            $target = User::find($request->input('id'));
        } elseif ($request->is('userdelete/*')) {
            $target = User::find($request->segment(2));
        }

        if ($target && in_array($target->login, $locked, true)) {
            $message = 'This is a shared demo account, so its password, login and role are locked.';
            return redirect()->back()->withErrors(['demo' => $message])->with('error', $message);
        }

        return $next($request);
    }
}
