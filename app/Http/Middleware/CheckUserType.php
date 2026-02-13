<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$types)
    {
        $user = Auth::user();
        if ($user && in_array($user->user_type, $types)) {
            return $next($request);
        }
        // Redirect or abort if user type does not match
        return redirect('/unauthorized'); // Or use abort(403) to return a 403 Forbidden response
    }
}
