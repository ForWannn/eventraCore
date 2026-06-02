<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('Admin')) {
            $allowedRoutes = [
                'dashboard',
                'logout',
                'users.index',
                'users.show',
                'users.create',
                'users.store',
                'users.edit',
                'users.update',
                'users.destroy',
                'events.index',
                'events.show',
                'settings.calendar',
                'settings.calendar.update',
            ];

            $routeName = $request->route() ? $request->route()->getName() : null;

            if ($routeName && !in_array($routeName, $allowedRoutes)) {
                abort(403, 'Akses ditolak untuk Admin.');
            }
        }

        return $next($request);
    }
}
