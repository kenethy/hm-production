<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = auth()->user();

        // If user is admin, allow access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // If user is staff, only allow access to specific resources
        if ($user->isStaff() && $role === 'staff') {
            // Get the current resource from the URL
            $segments = $request->segments();
            $resourceSegment = $segments[2] ?? null; // admin/[resource]/...

            // Allow access only to bookings and services
            $allowedResources = ['bookings', 'services'];

            if (in_array($resourceSegment, $allowedResources) || $resourceSegment === 'dashboard') {
                return $next($request);
            }

            // Redirect to dashboard if trying to access unauthorized resource
            return redirect()->route('filament.admin.pages.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // If user doesn't have the required role, redirect to dashboard
        return redirect()->route('filament.admin.pages.dashboard')
            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
