<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        // Disable auth redirects so API requests return JSON errors.
        Authenticate::redirectUsing(fn () => null);
        AuthenticationException::redirectUsing(fn () => null);

        // Log SQL queries (skip cache/Reverb queries, only log slow queries)
        if (app()->environment('local')) {

            DB::listen(function ($query) {
                // Skip cache table queries (Reverb restart checks, JWT token lookups)
                if (!config('app.debug_every_db_query')) {
                    if (str_contains($query->sql, '"cache"')) {
                        return;
                    }

                    // Only log slow queries (> 10ms) to reduce noise from polling
                    if ($query->time < 10) {
                        return;
                    }
                }

                Log::info('QUERY:');
                Log::info($query->sql);
                Log::info('BINDINGS:');
                Log::info($query->bindings);
                Log::info('TIME:');
                Log::info($query->time.'ms');
                Log::info('-------------------------');
            });
        }
    }
}
