<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServiceUnavailable
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('app.service_unavailable')) {
            abort(503);
        }

        return $response;
    }
}
