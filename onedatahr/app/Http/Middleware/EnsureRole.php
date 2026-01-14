<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Roles parameter can be a single role or multiple roles separated by | or ,
     * e.g. 'admin' or 'admin|manager' or 'admin,manager'.
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = Auth::user();

        if (!$user) {
            // Not authenticated — redirect to signin
            return redirect()->route('signin');
        }

        if ($roles) {
            $allowed = preg_split('/[|,]/', $roles);
            $allowed = array_map('trim', $allowed);

            if (!$user->hasAnyRole($allowed)) {
                abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
            }
        }

        return $next($request);
    }
}
