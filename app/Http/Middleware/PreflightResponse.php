<?php

namespace App\Http\Middleware;

use Closure;

class PreflightResponse
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

	    if ($request->getMethod() === "OPTIONS" || $request->getMethod() === "DELETE") {
		    return $next($request)
			    ->header('Access-Control-Allow-Origin', '*')
			    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	    }

        return $next($request);
    }
}
