<?php

namespace App\Jobs\Backups;

use App\Models\Backups\Rsync;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class TransferToOffsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('transfer_to_offsite');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // transfer database
        exec('rsync -chavzP -e ssh --delete --ignore-existing --stats /mnt/vol2/backups/ --exclude "scripts" mike@162.244.66.22:/mnt/sdb/storage/mysql', $output);

        $rsync = new Rsync;
        $rsync -> site = 'All';
        $rsync -> backup_type = 'database';
        $rsync -> response = json_encode($output);
        $rsync -> save();
    }
}
