<?php
// Disable output buffering
ob_start();

// Load Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

echo "<h1>Filament Diagnostic Tool</h1>";

// Check if Filament is installed
echo "<h2>Filament Installation</h2>";
if (class_exists('Filament\FilamentServiceProvider')) {
    echo "<p style='color:green'>Filament is installed.</p>";
} else {
    echo "<p style='color:red'>Filament is not installed.</p>";
}

// Check Filament version
echo "<h2>Filament Version</h2>";
if (class_exists('Filament\FilamentServiceProvider')) {
    $composerLock = json_decode(file_get_contents(__DIR__ . '/../composer.lock'), true);
    $filamentVersion = "Unknown";

    foreach ($composerLock['packages'] as $package) {
        if ($package['name'] === 'filament/filament') {
            $filamentVersion = $package['version'];
            break;
        }
    }

    echo "<p>Filament version: $filamentVersion</p>";
} else {
    echo "<p>Cannot determine Filament version.</p>";
}

// Check Filament routes
echo "<h2>Filament Routes</h2>";
$routes = app('router')->getRoutes();
$filamentRoutes = [];

foreach ($routes as $route) {
    $routeName = $route->getName();
    if ($routeName && strpos($routeName, 'filament') !== false) {
        $filamentRoutes[] = [
            'name' => $routeName,
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName(),
        ];
    }
}

if (count($filamentRoutes) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Name</th><th>URI</th><th>Methods</th><th>Action</th></tr>";

    foreach ($filamentRoutes as $route) {
        echo "<tr>";
        echo "<td>" . $route['name'] . "</td>";
        echo "<td>" . $route['uri'] . "</td>";
        echo "<td>" . $route['methods'] . "</td>";
        echo "<td>" . $route['action'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='color:red'>No Filament routes found.</p>";
}

// Check Filament configuration
echo "<h2>Filament Configuration</h2>";
if (file_exists(__DIR__ . '/../config/filament.php')) {
    echo "<p style='color:green'>Filament configuration file exists.</p>";

    $filamentConfig = config('filament');
    echo "<pre>";
    print_r($filamentConfig);
    echo "</pre>";
} else {
    echo "<p style='color:red'>Filament configuration file does not exist.</p>";
}

// Check Filament providers
echo "<h2>Filament Service Providers</h2>";
$providers = $app->getLoadedProviders();
$filamentProviders = [];

foreach ($providers as $provider => $loaded) {
    if (strpos($provider, 'Filament') !== false) {
        $filamentProviders[] = $provider;
    }
}

if (count($filamentProviders) > 0) {
    echo "<ul>";
    foreach ($filamentProviders as $provider) {
        echo "<li>$provider</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>No Filament service providers loaded.</p>";
}

// Check Filament middleware
echo "<h2>Filament Middleware</h2>";
try {
    $router = app('router');
    $routes = $router->getRoutes();

    // Find a Filament route to check its middleware
    $filamentRoute = null;
    foreach ($routes as $route) {
        $routeName = $route->getName();
        if ($routeName && strpos($routeName, 'filament') !== false) {
            $filamentRoute = $route;
            break;
        }
    }

    if ($filamentRoute) {
        echo "<p>Middleware for route: " . $filamentRoute->uri() . "</p>";
        echo "<ul>";
        foreach ($filamentRoute->middleware() as $mw) {
            echo "<li>$mw</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No Filament route found to check middleware.</p>";
    }

    // Check middleware groups
    echo "<p>Middleware Groups:</p>";
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $middlewareGroups = property_exists($kernel, 'middlewareGroups') ? $kernel->middlewareGroups : [];
    echo "<pre>";
    print_r($middlewareGroups);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error checking middleware: " . $e->getMessage() . "</p>";
}

// Check authentication configuration
echo "<h2>Authentication Configuration</h2>";
$authConfig = config('auth');
echo "<pre>";
print_r($authConfig);
echo "</pre>";

// Check session configuration
echo "<h2>Session Configuration</h2>";
$sessionConfig = config('session');
echo "<pre>";
print_r($sessionConfig);
echo "</pre>";

// Flush the output buffer
ob_end_flush();
