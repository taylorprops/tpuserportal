<?php

namespace App\Http\Controllers\DocManagement\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Archives\Documents;
use App\Models\DocManagement\Archives\Transactions;


class ArchivedTransactionsController extends Controller
{

    public function transactions_archived(Request $request) {

        return view('/doc_management/transactions/archived/transactions_archived');

    }

    public function get_transactions_archived(Request $request) {

        $direction = 'desc';
        $sort = 'actualClosingDate';
        if($request -> direction) {
            $direction = $request -> direction;
        }
        if($request -> sort) {
            $sort = $request -> sort;
        }
        $search = $request -> search ?? null;
        $transactions = Transactions::select(['listingGuid', 'saleGuid', 'agent_name', 'listingDate', 'actualClosingDate', 'status', 'address', 'city', 'state', 'zip'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('address', 'like', '%'.$search.'%')
                -> orWhere('agent_name', 'like', '%'.$search.'%');
            }
        })
        -> orderBy($sort, $direction)
        //-> sortable()
        -> paginate(25);

        return view('/doc_management/transactions/archived/get_transactions_archived_html', compact('transactions'));

    }


    public function transactions_archived_view(Request $request) {

        $transaction = Transactions::where('listingGuid', $request -> listingGuid)
        -> where('saleGuid', $request -> saleGuid)
        -> with(['agent_details:id,nickname,last,cell_phone,email1', 'docs'])
        -> first();

        $property = json_decode($transaction -> property);
        $address = $property -> streetNumber.' '.$property -> streetAddress.' '.$property -> city.', '.$property -> state.' '.$property -> zip;

        $agent = $transaction -> agent_details;
        $docs = $transaction -> docs;

        return view('/doc_management/transactions/archived/transactions_archived_view', compact('transaction', 'address', 'agent', 'docs'));

    }





}
