<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeritageFinancial\Loans;

class SearchController extends Controller
{
    public function search(Request $request) {

        $value = $request -> value;
        $group = auth() -> user() -> group;

        if($group == 'mortgage') {

            $loans = Loans::where(function($query) use ($value) {
                $query -> where('borrower_fullname', 'like', '%'.$value.'%')
                -> orWhere('co_borrower_fullname', 'like', '%'.$value.'%')
                -> orWhere('street', 'like', '%'.$value.'%');
            })
            -> orderBy('settlement_date', 'desc')
            -> get();
        }

        return view('search/search_results_'.$group.'_html', compact('loans'));

    }
}
