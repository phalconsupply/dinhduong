<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Auth;
use Closure;
class AdminAuthenticate extends Middleware
{
    public function handle($request, Closure $next, ... $roles)
    {
//        $_SESSION['ckfinder_auth'] = false;
//        $_SESSION['ckfinder_folder'] = 'app';
//        session('ckfinder_auth', false);
//        session('ckfinder_folder', 'app');
        if(Auth::check()) {
            if (!Auth::user()->hasAnyRole(['admin'])) {
                abort(403);
            } else {
//                $_SESSION['ckfinder_auth'] = true;
                if(Auth::user()->username != 'admin'){
//                    $_SESSION['ckfinder_folder'] = Auth::user()->username;
//                    session('ckfinder_folder', Auth::user()->username);
                }
                return $next($request);
            }
        }

        return redirect()->route('admin.auth.login');
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('admin.auth.login');
    }
}
