<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $value = $request->value;
        $group = auth()->user()->group;

        if ($group == 'mortgage' || $group == 'in_house') {
            $loans = Loans::where(function ($query) use ($value) {
                $query->where('borrower_fullname', 'like', '%'.$value.'%')
                ->orWhere('co_borrower_fullname', 'like', '%'.$value.'%')
                ->orWhere('street', 'like', '%'.$value.'%');
            })
            ->with(['loan_officer_1:id,fullname'])
            ->orderBy('settlement_date', 'desc')
            ->get();
        }

        if (count($loans) > 0) {
            return view('search/search_results_'.$group.'_html', compact('loans'));
        }

        return null;
    }
}
