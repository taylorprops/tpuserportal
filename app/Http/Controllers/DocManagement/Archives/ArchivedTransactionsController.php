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

        $escrow = null;
        $escrow_total_in = null;
        $escrow_total_out = null;
        $escrow_total_left = null;
        $checks = null;
        $transferred_link = null;

        $transaction = Transactions::where('listingGuid', $request -> listingGuid)
        -> where('saleGuid', $request -> saleGuid)
        -> with(['agent_details:id,nickname,last,cell_phone,email1', 'docs'])
        -> first();

        $address = $transaction -> address.' '.$transaction -> city.', '.$transaction -> state.' '.$transaction -> zip;

        $agent = $transaction -> agent_details;

        if($request -> saleGuid != '0') {
            $docs = Documents::where('saleGuid', $request -> saleGuid) -> get();
        } else {
            $docs = Documents::where('listingGuid', $request -> listingGuid) -> get();
        }

        $transactionId = $transaction -> transactionId;
        $mls = $transaction -> mlsNumber;

        $transferred_from_link = null;
        $transferred_to_link = null;
        $transferred_from = null;
        $transferred_to = null;
        $transferred_from_transactionId = null;
        $transferred_to_transactionId = null;
        $transferred_from_mls = null;
        $transferred_to_mls = null;
        $select = ['listingGuid', 'saleGuid', 'address','city','state','zip'];

        if($transactionId > 0) {

            $escrow = Escrow::where('TransactionId', $transactionId)
            -> orWhere('transfer_TransactionId', $transactionId)
            -> orWhere('transfer2_TransactionId', $transactionId)
            -> with(['checks'])
            -> first();

            if($escrow) {

                if($transactionId == $escrow -> transfer2_TransactionId) {
                    $transferred_from_transactionId = $escrow -> transfer_TransactionId;
                } else if($transactionId == $escrow -> transfer_TransactionId) {
                    $transferred_from_transactionId = $escrow -> TransactionId;
                    if($escrow -> transfer2_TransactionId > 0) {
                        $transferred_to_transactionId = $escrow -> transfer2_TransactionId;
                    }
                } else {
                    if($escrow -> transfer_TransactionId > 0) {
                        $transferred_to_transactionId = $escrow -> transfer_TransactionId;
                    }
                }

                if($transferred_from_transactionId) {
                    $transferred_from = Transactions::select($select) -> where('transactionId', $transferred_from_transactionId) -> first();
                }
                if($transferred_to_transactionId) {
                    $transferred_to = Transactions::select($select) -> where('transactionId', $transferred_to_transactionId) -> first();
                }

            }

        } else {

            if($mls != '') {
                $escrow = Escrow::where('mls', $mls)
                -> orWhere('transfer_mls', $mls)
                -> orWhere('transfer2_mls', $mls)
                -> with(['checks'])
                -> first();

                if($escrow) {

                    if($mls == $escrow -> transfer2_mls) {
                        $transferred_from_mls = $escrow -> transfer_mls;
                    } else if($mls == $escrow -> transfer_mls) {
                        $transferred_from_mls = $escrow -> mls;
                        if($escrow -> transfer2_mls != '') {
                            $transferred_to_mls = $escrow -> transfer2_mls;
                        }
                    } else {
                        if($escrow -> transfer_mls != '') {
                            $transferred_to_mls = $escrow -> transfer_mls;
                        }
                    }

                    if($transferred_from_mls) {
                        $transferred_from = Transactions::select($select) -> where('mlsNumber', $transferred_from_mls) -> first();
                    }
                    if($transferred_to_mls) {
                        $transferred_to = Transactions::select($select) -> where('mlsNumber', $transferred_to_mls) -> first();
                    }

                }

            }

        }

        if($escrow) {

            if($transferred_from) {
                $transferred_from_link = '<a href="/transactions_archived_view/'.$transferred_from -> listingGuid.'/'.$transferred_from -> saleGuid.'" class="underline" target="_blank">'.$transferred_from -> address.' '.$transferred_from -> city.', '.$transferred_from -> state.' '.$transferred_from -> zip.'</a>';
            }
            if($transferred_to) {
                $transferred_to_link = '<a href="/transactions_archived_view/'.$transferred_to -> listingGuid.'/'.$transferred_to -> saleGuid.'" class="underline" target="_blank">'.$transferred_to -> address.' '.$transferred_to -> city.', '.$transferred_to -> state.' '.$transferred_to -> zip.'</a>';
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

        }

        return view('/doc_management/transactions/archived/transactions_archived_view', compact('transaction', 'address', 'agent', 'docs', 'escrow', 'escrow_total_in', 'escrow_total_out', 'escrow_total_left', 'checks', 'transferred_from_link', 'transferred_to_link'));

    }

}
