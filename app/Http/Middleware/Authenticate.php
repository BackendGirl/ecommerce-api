<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    //to handle unuthorirized users
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login'); 
        }

        return null; 
    }
}
