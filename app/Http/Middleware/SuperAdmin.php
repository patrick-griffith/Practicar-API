<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdmin
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role != 'super') {            
            return response()->json(['error' => 'Your account does not have sufficient permissions to perform this action.'], 403);
        }

        return $next($request);
    }

}