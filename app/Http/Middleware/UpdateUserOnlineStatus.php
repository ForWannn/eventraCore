<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserOnlineStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Set online status in cache for 5 minutes
            $expiresAt = now()->addMinutes(5);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);

            // Throttle database write: only update last_seen_at if it's null or older than 1 minute
            if (is_null($user->last_seen_at) || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                $now = now();
                \App\Models\User::where('id', $user->id)->update(['last_seen_at' => $now]);
                $user->last_seen_at = $now;
            }
        }

        return $next($request);
    }
}
