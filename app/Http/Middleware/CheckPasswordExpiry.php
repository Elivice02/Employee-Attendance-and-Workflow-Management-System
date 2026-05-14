<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordExpiry
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Skip for change-password route
            if ($request->routeIs('password.change', 'password.change.update')) {
                return $next($request);
            }

            // Check if password must be changed on first login
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'You must change your password before continuing.');
            }

            // Check if password is expired (3 months)
            if ($user->password_changed_at) {
                $lastChanged = $user->password_changed_at;
                $expiryDate = $lastChanged->addMonths(3);

                if (now() > $expiryDate) {
                    return redirect()->route('password.change')
                        ->with('danger', 'Your password has expired. Please change it.');
                }

                // Warn if password will expire in 7 days
                if (now() > $expiryDate->subDays(7)) {
                    session()->flash('info', 'Your password will expire in ' . now()->diffInDays($expiryDate) . ' days. Please update it soon.');
                }
            }
        }

        return $next($request);
    }
}
