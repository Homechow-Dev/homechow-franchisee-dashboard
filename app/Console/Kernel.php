<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () { DB::table('telescope_entries')->delete(); })->hourly();
        $schedule->call(function () { DB::table('telescope_entries_tags')->delete(); })->hourly();
        $schedule->call(function () { DB::table('machines')->delete(); })->everyThreeHours($minutes = 0);
        $schedule->call(function () { DB::table('temps')->delete(); })->everyThreeHours($minutes = 0);
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
        
    }
}
