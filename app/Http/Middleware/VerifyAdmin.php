<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); 

        // Check if user authenticated 
        //if user token is also created then one condition can be added to check isAdmin==1
        if (!$user || !$user->currentAccessToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Token not found or invalid.',
            ], 401);
        }
        return $next($request);
    }
}
