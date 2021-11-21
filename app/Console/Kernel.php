<?php

namespace App\Console;

use Illuminate\Support\Facades\App;
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
        // update bright mls agents
        $schedule -> command('bright_mls:update_agents') -> hourly();
        // update bright mls offices
        $schedule -> command('bright_mls:update_offices') -> hourly();
        // remove bright mls agents
        $schedule -> command('bright_mls:remove_agents') -> everyFiveMinutes();

        // %%% TEMP %%% //

        // add transactions from skyslope to db
        // ends - when no more transactions added to skyslope
        $schedule -> command('archives:get_transactions') -> everySixHours();
        // add documents to skyslope transactions
        // ends - when no more transactions added to skyslope
        $schedule -> command('archives:add_documents') -> everyFourHours();
        // add missing documents to skyslope transactions
        // ends - when no more transactions added to skyslope
        $schedule -> command('archives:add_missing_documents') -> hourly();
        // add skyslope data to old db
        // ends - when no more transactions added to skyslope
        $schedule -> command('old_db:add_skyslope_listings') -> everyMinute();

        // merge agent home addresses with bright agents
        //$schedule -> command('agent_addresses:merge') -> everyMinute();


        // add mls_company to skyslope
        // ends - when all data added
        //$schedule -> command('archives:add_mls_company_transactions') -> everyMinute();
        //$schedule -> command('archives:add_mls_company_missing_documents') -> everyMinute();
        //$schedule -> command('archives:add_missing_fields') -> everyMinute();

        // add escrow checks
        // ends - when no more transactions added to skyslope
        //if(env('APP_EVN') == 'local') {
            //$schedule -> command('archives:get_escrow_checks') -> everyMinute();
        //}

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
