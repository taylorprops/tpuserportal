<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {

        $group = auth() -> user() -> group;

        // Mortgage
        if ($group == 'mortgage') {

            $active_loans = Loans::orderBy('settlement_date', 'desc')
            -> get();

            return view('/dashboard/dashboard_'.$group, compact('group', 'active_loans'));
        }



    }
}
