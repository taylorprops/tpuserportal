<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Admin\QueueMonitor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function system_monitor(Request $request)
    {

        // mysql backups

        if(config('app.env') == 'local') {

            $mysql_backups_tp = [];
            $mysql_backups_hf = [];
            $class_tp = 'bg-green-100 text-green-800';
            $class_hf = 'bg-green-100 text-green-800';

        } else {

            $mysql_backups_tp = Storage::disk('staging_backups') -> files('mysql/TPUserPortal');
            $mysql_backups_hf = Storage::disk('staging_backups') -> files('mysql/HeritageFinancial');

            $mysql_backups_tp = $this -> sort_files($mysql_backups_tp);
            $mysql_backups_hf = $this -> sort_files($mysql_backups_hf);

            // if no backup for today show bg-red
            $class_tp = 'bg-green-100 text-green-800';
            if(substr($mysql_backups_tp[0]['file_name'], 0, 10) != date('Y-m-d')) {
                $class_tp = 'bg-red-100 text-red-800';
            }

            $class_hf = 'bg-green-100 text-green-800';
            if(substr($mysql_backups_hf[0]['file_name'], 0, 10) != date('Y-m-d')) {
                $class_hf = 'bg-red-100 text-red-800';
            }

        }





        return view('/admin/system_monitor', compact('mysql_backups_tp', 'mysql_backups_hf', 'class_tp', 'class_hf'));

    }

    public function get_failed_jobs(Request $request) {

        // queue monitor
        $queue_failed_jobs = QueueMonitor::where('failed', '1')
        -> orderBy('id', 'DESC')
        -> get();

        return view('/admin/data/get_failed_jobs_html', compact('queue_failed_jobs'));

    }

    public function delete_failed_jobs(Request $request) {

        $ids = explode(',', $request -> checked);
        QueueMonitor::whereIn('id', $ids) -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function queue_monitor(Request $request)
    {
        return view('/admin/queue_monitor');
    }

    public function sort_files($files) {

        $data = [];
        foreach($files as $file) {
            $file_name = str_replace('.zip', '', substr($file, strrpos($file, '/') + 1));
            $file_name = substr($file_name, 0, 10).' '.str_replace('-', ':', substr($file_name, 11));
            $size = Helper::convert_bytes(Storage::disk('staging_backups') -> size($file), 'M');
            $file_info = [
                'file_name' => $file_name,
                'size' => $size
            ];

            $data[] = $file_info;
        }


        $keys = array_column($data, 'file_name');
        array_multisort($keys, SORT_DESC, $data);

        return $data;

    }

}
