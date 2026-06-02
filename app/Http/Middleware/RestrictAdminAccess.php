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
        if (Auth::check() && Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Superadmin')) {
            $allowedRoutesWithoutPermission = [
                'logout',
                'events.index',
                'events.show',
            ];

            $routeName = $request->route() ? $request->route()->getName() : null;

            if ($routeName) {
                // If it is globally allowed for Admin, pass
                if (in_array($routeName, $allowedRoutesWithoutPermission)) {
                    return $next($request);
                }

                // Map of routes to their required permissions
                $routePermissionMap = [
                    'dashboard' => 'view_dashboard',
                    'users.index' => 'crud_users',
                    'users.show' => 'crud_users',
                    'users.create' => 'crud_users',
                    'users.store' => 'crud_users',
                    'users.edit' => 'crud_users',
                    'users.update' => 'crud_users',
                    'users.destroy' => 'crud_users',

                    'settings.calendar' => 'manage_calendar',
                    'settings.calendar.update' => 'manage_calendar',

                    'attendance.recap' => 'rekap_absen',
                    'attendance.recap.export' => 'rekap_absen',

                    'weekly.recap' => 'rekap_weekly',
                    'weekly.recap.export' => 'rekap_weekly',
                    'weekly.show_user' => 'rekap_weekly',

                    'weekly.history' => 'weekly_history',

                    'leave-approvals.index' => 'leave_approvals',
                    'leave-approvals.approve' => 'leave_approvals',
                    'leave-approvals.reject' => 'leave_approvals',

                    'events.create' => 'crud_events',
                    'events.store' => 'crud_events',
                    'events.destroy' => 'crud_events',
                ];

                if (array_key_exists($routeName, $routePermissionMap)) {
                    $requiredPermission = $routePermissionMap[$routeName];
                    if (Auth::user()->can($requiredPermission)) {
                        return $next($request);
                    }
                }

                abort(403, 'Akses ditolak untuk Admin.');
            }
        }

        return $next($request);
    }
}
