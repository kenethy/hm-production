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
        //
    }
}
