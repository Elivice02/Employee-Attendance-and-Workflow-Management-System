<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ FIX HERE

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $allowedRoles = collect($roles)
            ->flatMap(fn ($role) => explode(',', $role))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->all();

        if (!in_array(Auth::user()->role, $allowedRoles, true)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
