<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // clear failed jobs -
        $schedule -> command('queue:flush')
        -> dailyAt('2:00')
        -> timezone('America/New_York')
        -> environments(['production']);

        // Backups
        $schedule -> command('backup:clean')
        -> dailyAt('02:00')
        -> timezone('America/New_York')
        -> environments(['production']);

        $schedule -> command('backup:run --only-db')
        -> timezone('America/New_York')
        -> twiceDaily(2, 18)
        -> environments(['production']);

        // prune failed jobs
        $schedule -> command('queue:prune-failed')
        -> timezone('America/New_York')
        -> everyTwoHours();

        // update bright agents and offices
        $schedule -> command('bright_mls:update_agents_and_offices')
        -> timezone('America/New_York')
        -> everyTwoHours()
        -> environments('production');

        // add missing agents from bright
        $schedule -> command('bright_mls:find_missing_agents')
        -> timezone('America/New_York')
        -> dailyAt('03:00')
        -> environments('production');

        // add bright listings
        $schedule -> command('bright_mls:add_listings')
        -> timezone('America/New_York')
        -> hourly()
        -> environments('production');

        // update bright listings
        $schedule -> command('bright_mls:update_listings')
        -> timezone('America/New_York')
        -> hourly()
        -> environments('production');

        // cancel bright listings
        $schedule -> command('bright_mls:cancel_listings')
        -> timezone('America/New_York')
        -> everyThirtyMinutes()
        -> environments('production');

        // update db with missing from bright
        $schedule -> command('bright_mls:update_missing_from_bright')
        -> timezone('America/New_York')
        -> hourly()
        -> environments('production');

        // update db with missing from db
        $schedule -> command('bright_mls:update_missing_from_db')
        -> timezone('America/New_York')
        -> hourly()
        -> environments('production');

        // Schedule - update status to complete on event date
        $schedule -> command('schedule:set_status_to_complete')
        -> timezone('America/New_York')
        -> dailyAt('09:00')
        -> environments('production');



        // %%% TEMP %%% //

        // add bright fields
        // $schedule -> command('bright_mls:add_fields')
        // -> timezone('America/New_York')
        // -> everyMinute()
        // -> environments('production');

        // add transactions from skyslope to db
        // ends - when no more transactions added to skyslope
        $schedule -> command('archives:get_transactions') -> everySixHours() -> environments('production');
        // add documents to skyslope transactions
        // ends - when no more transactions added to skyslope
        // $schedule -> command('archives:add_documents') -> everyFourHours() -> environments('production');
        // add missing documents to skyslope transactions
        // ** no longer available
        // ends - when no more transactions added to skyslope
        // $schedule -> command('archives:add_missing_documents') -> hourly() -> environments('production');
        // add skyslope data to old db
        // ** no longer available
        // ends - when no more transactions added to skyslope
        $schedule -> command('old_db:add_skyslope_listings') -> everyFiveMinutes() -> environments('production') -> withoutOverlapping();

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
