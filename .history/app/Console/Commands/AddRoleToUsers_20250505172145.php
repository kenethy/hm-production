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
        //
    }
}
