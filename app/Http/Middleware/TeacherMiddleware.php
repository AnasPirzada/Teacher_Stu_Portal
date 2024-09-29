<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Check if the authenticated user is a teacher
        if (Auth::check() && Auth::user()->role === 'teacher') {
            return $next($request);
        }

        // Redirect to home if the user is not a teacher
        return redirect('/home')->withErrors(['access_denied' => 'You do not have access to this section.']);
    }
}
