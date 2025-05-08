<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CreateAdminUser::class,
        Commands\CreateStaffUser::class,
        Commands\AddRoleToUsers::class,
        Commands\CheckHttpsConfig::class,
        Commands\FixMechanicReports::class,
        Commands\GenerateMechanicReports::class,
        Commands\RegenerateMechanicReports::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate mechanic reports every Monday at 00:01
        $schedule->command('mechanic:generate-reports')
            ->weeklyOn(1, '00:01')
            ->appendOutputTo(storage_path('logs/mechanic-reports.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
