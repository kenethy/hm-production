<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FixLivewireRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log the request for debugging
        Log::info('FixLivewireRoutes middleware processing request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'is_livewire_update' => $request->is('livewire/update'),
        ]);

        // Check if this is a GET request to livewire/update
        if ($request->method() === 'GET' && $request->is('livewire/update')) {
            // Redirect to the previous page or home
            Log::warning('Redirecting GET request to livewire/update to previous page');
            return redirect()->back()->with('error', 'Invalid request method for Livewire update');
        }

        return $next($request);
    }
}
