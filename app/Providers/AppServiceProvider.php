<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Default authenticated API rate limiter (120/min)
         */
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        /**
         * Public List rate limiter (60/min)
         */
        RateLimiter::for('public_list', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        /**
         * Search limiter (30/min)
         */
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        /**
         * Mutation (Create/Update) limiter (20/min)
         */
        RateLimiter::for('mutation', function (Request $request) {
            return Limit::perMinute(20)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        /**
         * Login / OTP limiter (5/min)
         */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
