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
        // telescope error finding - clear database
        $schedule -> command('telescope:prune') -> daily();

        // %%% TEMP %%% //

        // add transactions from skyslope to db
        // ends - when no more transactions added to skyslope
        $schedule -> command('skyslope:get_transactions') -> everySixHours();
        // add documents to skyslope transactions
        // ends - when no more transactions added to skyslope
        $schedule -> command('skyslope:add_documents') -> everyTenMinutes();
        // add missing documents to skyslope transactions
        // ends - when no more transactions added to skyslope
        $schedule -> command('skyslope:add_missing_documents') -> hourly();
        // add skyslope data to old db
        // ends - when no more transactions added to skyslope
        $schedule -> command('old_db:add_skyslope_listings') -> everyMinute();

        // add mls_company to skyslope
        // ends - when all data added
        //$schedule -> command('skyslope:add_mls_company_transactions') -> everyMinute();
        //$schedule -> command('skyslope:add_mls_company_missing_documents') -> everyMinute();

        // %%% END TEMP %%% //
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
