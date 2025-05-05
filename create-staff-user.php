<?php

// Script untuk membuat user staff dengan akses terbatas

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Start the session
$session = $app->make('session');
$session->start();

// Set the email and password
$email = 'hartonomotor1979@user.com';
$password = 'hmbengkel1979user';
$name = 'Hartono Motor Staff';

echo "Creating staff user...\n";

// Check if the user exists
$userModel = config('auth.providers.users.model');
$user = $userModel::where('email', $email)->first();

if ($user) {
    echo "User with email $email already exists!\n";
    
    // Update the user's role to staff
    $user->update(['role' => 'staff']);
    echo "User role updated to staff.\n";
} else {
    // Create the user
    $user = new $userModel();
    $user->name = $name;
    $user->email = $email;
    $user->password = app('hash')->make($password);
    $user->role = 'staff';
    $user->save();
    
    echo "Staff user created successfully!\n";
}

echo "Email: $email\n";
echo "Password: $password\n";
echo "This user has limited access to only Services and Bookings resources.\n";
