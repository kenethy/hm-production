<?php

namespace App\Providers;

use App\Models\Service;
use App\Observers\MechanicServiceObserver;
use App\Observers\ServiceObserver;
use Illuminate\Database\Eloquent\Relations\Pivot;
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
        $this->app['url']->assetUrl = function ($root, $path, $secure = null) {
            // Ignore unused parameters
            unset($root, $secure);
            return url($path, [], true);
        };
    }
}
