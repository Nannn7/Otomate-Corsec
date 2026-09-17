<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMustChangePassword
{
    /**
     * Route yang tetap boleh diakses walau user masih wajib ganti password
     * (halaman reset itu sendiri, dan logout).
     */
    private const EXEMPT_ROUTES = [
        'password.force_reset',
        'password.force_reset.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $routeName = $request->route()?->getName();

            if (!in_array($routeName, self::EXEMPT_ROUTES, true)) {
                return redirect()->route('password.force_reset');
            }
        }

        return $next($request);
    }
}