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
    // public function handle(Request $request, Closure $next, $roles = null)
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         // Not authenticated — redirect to signin
    //         return redirect()->route('signin');
    //     }

    //     if ($roles) {
    //         $allowed = preg_split('/[|,]/', $roles);
    //         $allowed = array_map('trim', $allowed);

    //         if (!in_array($user->role, $allowed, true)) {
    //             abort(403, 'Unauthorized');
    //         }
    //     }

    //     return $next($request);
    // }
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin');
        }

        if ($roles) {
            // Pecah string 'admin,superadmin' menjadi array ['admin', 'superadmin']
            $allowed = preg_split('/[|,]/', $roles); // Menghasilkan array
            $allowed = array_map('trim', $allowed);

            if (!$user->hasRole($allowed)) { // Mengecek apakah user punya salah satu dari array tersebut
                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
