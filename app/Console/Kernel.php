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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule -> command('telescope:prune') -> daily();
        // add transactions from skyslope to db
        $schedule -> command('skyslope:get_transactions') -> everySixHours();
        // add documents to skyslope transactions
        $schedule -> command('skyslope:add_documents') -> everyTenMinutes();
        // add missing documents to skyslope transactions
        $schedule -> command('skyslope:add_missing_documents') -> everyHour();
        // add skyslope data to old db
        //$schedule -> command('old_db:add_skyslope_listings') -> everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this -> load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
