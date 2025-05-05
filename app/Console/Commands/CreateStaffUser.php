<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateStaffUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-staff-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a staff user for Filament with limited permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'hartonomotor1979@user.com';
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info('Staff user already exists!');

            // Update the user's role to staff
            $user->update(['role' => 'staff']);
            $this->info('User role updated to staff.');

            return;
        }

        User::create([
            'name' => 'Hartono Motor Staff',
            'email' => $email,
            'password' => Hash::make('hmbengkel1979user'),
            'role' => 'staff',
        ]);

        $this->info('Staff user created successfully!');
        $this->info('Email: hartonomotor1979@user.com');
        $this->info('Password: hmbengkel1979user');
    }
}
