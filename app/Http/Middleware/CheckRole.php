<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        foreach($roles as $role) {
            
            if($user->hasRole($role))
                return $next($request);
        }
        
        if (Request::isMethod('get')) {
            abort(401);
        } else if(Request::isMethod('post')) {
            return redirect('/');
        }

        
    }
}