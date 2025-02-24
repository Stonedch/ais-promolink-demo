<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserUnActive
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (@$request->user() && @$request->user()->is_active == false) {
            Auth::logout();
        }

        return $response;
    }
}
