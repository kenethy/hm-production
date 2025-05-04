<?php
// This is a simple script to test the login functionality
// It will attempt to log in with the provided credentials and redirect to the admin panel

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get the auth service
$auth = $app->make('auth');

// Attempt to log in
if ($auth->attempt([
    'email' => 'hartonomotor1979@gmail.com',
    'password' => 'hmbengkel1979',
])) {
    // Redirect to the admin panel
    header('Location: /admin');
    exit;
} else {
    echo "Login failed. Please check your credentials.";
}
