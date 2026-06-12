<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $publicHtmlPath = base_path('../public_html');
        if (file_exists($publicHtmlPath) && is_dir($publicHtmlPath)) {
            $this->app->usePublicPath($publicHtmlPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $restrictedAbilities = [
            'leave_request',
            'leave_approvals',
            'crud_events',
            'rekap_absen',
            'rekap_weekly',
            'rekap_event',
            'attendance_history',
            'weekly_history',
        ];

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) use ($restrictedAbilities) {
            if ($user->hasRole('Superadmin')) {
                if (in_array($ability, $restrictedAbilities)) {
                    return false;
                }
                return true;
            }
            return null;
        });
    }
}
