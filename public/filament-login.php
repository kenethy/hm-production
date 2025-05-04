<?php

// This script will attempt to log in to Filament directly without using the form

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Start the session
$session = $app->make('session');
$session->start();

// Get the auth service
$auth = $app->make('auth');

// Set the email and password
$email = 'hartonomotor1979@gmail.com';
$password = 'hmbengkel1979';

echo "<h1>Filament Direct Login</h1>";

// Check if the user exists
$userModel = config('auth.providers.users.model');
$user = $userModel::where('email', $email)->first();

if (!$user) {
    echo "<p style='color:red'>User with email $email does not exist!</p>";
    
    // Create the user
    echo "<p>Creating user...</p>";
    $user = new $userModel();
    $user->name = 'Hartono Motor Admin';
    $user->email = $email;
    $user->password = app('hash')->make($password);
    $user->save();
    
    echo "<p style='color:green'>User created successfully!</p>";
}

// Attempt to log in
if ($auth->attempt(['email' => $email, 'password' => $password])) {
    // Regenerate the session
    $session->regenerate();
    
    echo "<p style='color:green'>Login successful!</p>";
    echo "<p>You are now logged in as: " . $auth->user()->name . " (" . $auth->user()->email . ")</p>";
    echo "<p><a href='/admin'>Go to Admin Panel</a></p>";
} else {
    echo "<p style='color:red'>Login failed! Please check your credentials.</p>";
    
    // Try to authenticate manually
    echo "<p>Trying manual authentication...</p>";
    
    if ($user && app('hash')->check($password, $user->password)) {
        $auth->login($user);
        $session->regenerate();
        
        echo "<p style='color:green'>Manual login successful!</p>";
        echo "<p>You are now logged in as: " . $auth->user()->name . " (" . $auth->user()->email . ")</p>";
        echo "<p><a href='/admin'>Go to Admin Panel</a></p>";
    } else {
        echo "<p style='color:red'>Manual login failed! Password does not match.</p>";
        
        // Show password hash for debugging
        echo "<p>Password hash in database: " . $user->password . "</p>";
        echo "<p>Generated hash for comparison: " . app('hash')->make($password) . "</p>";
    }
}
