<?php

namespace App\Http\Controllers\DocManagement\Archives;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Archives\Escrow;
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
        $transactions = Transactions::select(['listingGuid', 'saleGuid', 'agent_name', 'listingDate', 'actualClosingDate', 'status', 'address', 'city', 'state', 'zip', 'data_source'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('address', 'like', '%'.$search.'%')
                -> orWhere('agent_name', 'like', '%'.$search.'%');
            }
        })
        -> with(['docs:id,listingGuid,saleGuid,fileName'])
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

        $address = $transaction -> address.' '.$transaction -> city.', '.$transaction -> state.' '.$transaction -> zip;

        $agent = $transaction -> agent_details;
        $docs = $transaction -> docs;

        if($transaction -> transactionId > 0) {
            $escrow = Escrow::where('TransactionId', $transaction -> transactionId) -> with(['checks']) -> first();
        } else {
            $escrow = Escrow::where('mls', $transaction -> mlsNumber) -> with(['checks']) -> first();
        }

        $checks = $escrow -> checks;

        $escrow_total_in = $checks -> where('cleared', 'yes')
        -> where('amount', '>', '0')
        -> where('check_type', 'in')
        -> sum('amount');

        $escrow_total_out = $checks -> where('cleared', 'yes')
        -> where('amount', '>', '0')
        -> where('check_type', 'out')
        -> sum('amount');

        $escrow_total_left = $escrow_total_in - $escrow_total_out;

        $escrow_total_in = '$'.number_format($escrow_total_in, 0);
        $escrow_total_out = '$'.number_format($escrow_total_out, 0);
        $escrow_total_left = '$'.number_format($escrow_total_left, 0);

        return view('/doc_management/transactions/archived/transactions_archived_view', compact('transaction', 'address', 'agent', 'docs', 'escrow', 'escrow_total_in', 'escrow_total_out', 'escrow_total_left', 'checks'));

    }

}
