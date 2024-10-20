<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAjaxRequest
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->ajax()) {
            return response('Access Denied', 403);
        }

        return $next($request);
    }
}