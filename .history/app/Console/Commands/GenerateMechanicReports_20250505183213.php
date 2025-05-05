<?php

namespace App\Console\Commands;

use App\Models\Mechanic;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMechanicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-mechanic-reports {--week= : The week to generate reports for (YYYY-MM-DD format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate weekly reports for mechanics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
