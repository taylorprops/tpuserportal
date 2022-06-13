<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleChecklistController extends Controller
{
    public function checklist(Request $request) {

        return view('/marketing/schedule/checklist');

    }

    public function get_checklist(Request $request) {



    }
}
