<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class CheckHttpsConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:https';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check HTTPS configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking environment settings...');
        $this->line('APP_URL: ' . config('app.url'));
        $this->line('ASSET_URL: ' . config('app.asset_url', 'Not set'));
        $this->line('APP_ENV: ' . config('app.env'));
        $this->line('HTTPS Forced: ' . (config('app.env') !== 'local' ? 'Yes' : 'No'));

        $this->newLine();
        $this->info('Checking URL generation...');
        $this->line('url(\'/\'): ' . url('/'));
        $this->line('asset(\'css/app.css\'): ' . asset('css/app.css'));
        $this->line('secure_url(\'/\'): ' . secure_url('/'));

        $this->newLine();
        $this->info('Checking Vite configuration...');
        try {
            $vite = app(\Illuminate\Foundation\Vite::class);
            $this->line('Vite class found: ' . get_class($vite));
            
            // Try to generate a Vite asset URL
            $url = $vite->asset('resources/css/app.css');
            $this->line('Vite asset URL for resources/css/app.css: ' . $url);
            
            $this->line('Vite configuration looks good!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('Checking ForceHttps middleware...');
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $middlewareGroups = $kernel->getMiddlewareGroups();
        $webMiddleware = $middlewareGroups['web'] ?? [];

        $forceHttpsFound = false;
        foreach ($webMiddleware as $middleware) {
            if (is_string($middleware) && strpos($middleware, 'ForceHttps') !== false) {
                $forceHttpsFound = true;
                $this->line('ForceHttps middleware is registered: ' . $middleware);
                break;
            }
        }

        if (!$forceHttpsFound) {
            $this->warn('ForceHttps middleware is NOT registered in the web middleware group.');
        }

        return Command::SUCCESS;
    }
}
