<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is an admin
        if (auth()->check() && auth()->user()->user_role == 1) {
            return $next($request);
        }

        // If the user is not an admin, redirect them to the dashboard or another appropriate page
        return redirect('dashboard')->with('error', 'You do not have access to this section.');
    }
}
