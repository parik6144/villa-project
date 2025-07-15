<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyUserEditPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $recordId = $request->route('record');
        $currentUserId = Auth::id();

        if ($recordId == $currentUserId || Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        abort(404);
    }
}
