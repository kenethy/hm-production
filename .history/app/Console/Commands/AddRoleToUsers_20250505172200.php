<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-role-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add role column to users table and set default role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding role column to users table...');

        try {
            if (!Schema::hasColumn('users', 'role')) {
                Schema::table('users', function ($table) {
                    $table->string('role')->default('admin')->after('email');
                });
                $this->info('Role column added successfully!');
            } else {
                $this->info('Role column already exists.');
            }

            // Set all existing users to admin role
            DB::table('users')->whereNull('role')->update(['role' => 'admin']);
            $this->info('All existing users set to admin role.');

            $this->info('Operation completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
