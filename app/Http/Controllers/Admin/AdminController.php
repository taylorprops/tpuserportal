<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function system_monitor(Request $request)
    {

        $files = Storage::disk('staging_backups') -> files('mysql/TPUserPortal');
        dd($files);
        return view('/admin/system_monitor');
    }

    public function queue_monitor(Request $request)
    {
        return view('/admin/queue_monitor');
    }
}
