<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {

        $group = auth() -> user() -> group;

        return view('/dashboard/dashboard_'.$group);

    }
}
