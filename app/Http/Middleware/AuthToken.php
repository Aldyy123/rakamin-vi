<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // dd($request->header('Authorization'), Auth::guard('api')->check());
        if ($request->header('Authorization') && Auth::guard('api')->check()) {
            return $next($request);
        }else{
            return response()->json([
                'message' => 'You are not authorized to access this resources',
            ], 404);
        }
    }
}
