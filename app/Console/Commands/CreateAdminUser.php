<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin-user';
    protected $description = 'Create an admin user for Filament';

    public function handle()
    {
        $user = User::where('email', 'hartonomotor1979@gmail.com')->first();
        
        if ($user) {
            $this->info('Admin user already exists!');
            return;
        }
        
        User::create([
            'name' => 'Hartono Motor Admin',
            'email' => 'hartonomotor1979@gmail.com',
            'password' => Hash::make('hmbengkel1979'),
        ]);
        
        $this->info('Admin user created successfully!');
    }
}
