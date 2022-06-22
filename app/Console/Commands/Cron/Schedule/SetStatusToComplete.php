<?php

namespace App\Console\Commands\Cron\Schedule;

use App\Models\Marketing\Schedule\Schedule;
use Illuminate\Console\Command;

class SetStatusToComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:set_status_to_complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status to complete on sent date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = Schedule::where('event_date', '<=', date('Y-m-d'))
            -> where('status_id', '33')
            -> update([
                'status_id' => '24',
            ]);
    }
}
