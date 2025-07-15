<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPropertyCreatePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('property_owner') || $user->hasRole('manager')) {
            return $next($request);
        }

        abort(403, 'You are not allowed to create properties.');
    }
}
