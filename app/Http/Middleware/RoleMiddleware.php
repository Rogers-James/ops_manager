<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = Auth::user();

        // assumes $user->roles() belongsToMany and role has slug
        $hasRole = $user->roles()->whereIn('slug', $roles)->exists();

        if (!$hasRole) {
            abort(403, 'You don’t have access.');
            // return redirect()->route('login')->with('error', 'Please login first.');
        }

        return $next($request);
    }
}
