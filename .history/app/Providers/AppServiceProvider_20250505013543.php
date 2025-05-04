<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS always
        URL::forceScheme('https');

        // Set asset URL to HTTPS
        $this->app['url']->assetUrl = function ($root, $path, $secure = null) use ($app) {
            // Ignore unused parameters
            unset($root, $secure);
            return url($path, [], true);
        };
    }
}
