<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiterMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $name = strtolower($request->input('name', ''));
        $ip = $request->ip() ?? '127.0.0.1';
        $key = 'login_throttle:' . md5($name . '|' . $ip);

        $timerKey = $key . ':timer';
        $countKey = $key . ':count';
        $lockoutKey = $key . ':lockouts';

        $lockoutUntil = Cache::get($timerKey);
        if ($lockoutUntil && now()->timestamp < (int) $lockoutUntil) {
            $seconds = (int) $lockoutUntil - now()->timestamp;
            return response()->json([
                'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                'retry_after' => $seconds,
            ], 429);
        }

        $response = $next($request);

        // If response indicates failed authentication (401, 422, or 400)
        if (in_array($response->getStatusCode(), [401, 422, 400])) {
            $attempts = ((int) Cache::get($countKey, 0)) + 1;
            Cache::put($countKey, $attempts, 3600);

            // Trigger exponential backoff if failed attempts reach 3 or more
            if ($attempts >= 3) {
                $lockoutCount = ((int) Cache::get($lockoutKey, 0)) + 1;
                Cache::put($lockoutKey, $lockoutCount, 86400);

                // Exponential backoff: 60s, 120s, 240s, 480s, etc. (max 1 hour / 3600s)
                $decaySeconds = (int) min(3600, 60 * pow(2, $lockoutCount - 1));
                $lockoutUntil = now()->timestamp + $decaySeconds;

                Cache::put($timerKey, $lockoutUntil, $decaySeconds);
                Cache::forget($countKey);

                return response()->json([
                    'message' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$decaySeconds} detik.",
                    'retry_after' => $decaySeconds,
                ], 429);
            }
        } elseif ($response->getStatusCode() === 200) {
            // Clear rate limiting tracking on successful login
            Cache::forget($timerKey);
            Cache::forget($countKey);
            Cache::forget($lockoutKey);
        }

        return $response;
    }
}
