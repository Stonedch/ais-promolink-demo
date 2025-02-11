<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogRoute
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $log = [
            'status' => $response->status(),
            'user' => $request->user() ?: null,
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'request_body' => $request->all(),
        ];

        Log::info(json_encode($log, JSON_UNESCAPED_UNICODE));

        return $response;
    }
}
