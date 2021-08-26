<?php

namespace App\Http\Controllers\DocManagement\Archives;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Archives\Escrow;
// use App\Models\OldDB\Company\Escrow as OldEscrow;
// use App\Models\DocManagement\Archives\EscrowChecks;

class EscrowController extends Controller
{

    public function escrow(Request $request) {

        return view('doc_management/transactions/archived/escrow');

    }

    public function get_escrow(Request $request) {

        $direction = 'desc';
        $sort = 'contract_date';
        if($request -> direction) {
            $direction = $request -> direction;
        }
        if($request -> sort) {
            $sort = $request -> sort;
        }
        $search = $request -> search ?? null;
        $escrows = Escrow::select(['mls', 'TransactionId', 'contract_date', 'agent', 'address', 'city', 'state', 'zip'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('address', 'like', '%'.$search.'%')
                -> orWhere('agent_name', 'like', '%'.$search.'%');
            }
        })
        -> with(['transaction_skyslope', 'transaction_company'])
        -> orderBy($sort, $direction)
        //-> sortable()
        -> paginate(25);

        return view('doc_management/transactions/archived/get_escrow_html', compact('escrows'));

    }


    //////// TRANSFER ARCHIVE /////////

    /* public function escrow(Request $request) {

        $escrows = OldEscrow::limit(1000) -> where('transferred_to_new_server', 'no') -> get();

        $escrow_ids = [];

        foreach($escrows as $escrow) {

            $escrow_ids[] = $escrow -> id;

            $add_escrow = new Escrow();

            foreach($escrow -> toArray() as $key => $value) {

                if($value == '0000-00-00') {
                    $value = '';
                }

                $add_escrow -> $key = $value;
            }

            $add_escrow -> save();

            if($escrow -> ck1_in_amount > 0) {
                $this -> add_check($escrow -> id, 'in', $escrow -> ck1_in_amount, $escrow -> ck1_in_cleared, $escrow -> ck1_in_date, $escrow -> ck1_in_loc, $escrow -> ck1_in_name, $escrow -> ck1_in_number, $escrow -> ck1_in_bounced, $escrow -> ck1_in_cleared_date, '');
            }
            if($escrow -> ck2_in_amount > 0) {
                $this -> add_check($escrow -> id, 'in', $escrow -> ck2_in_amount, $escrow -> ck2_in_cleared, $escrow -> ck2_in_date, $escrow -> ck2_in_loc, $escrow -> ck2_in_name, $escrow -> ck2_in_number, $escrow -> ck2_in_bounced, $escrow -> ck2_in_cleared_date, '');
            }
            if($escrow -> ck3_in_amount > 0) {
                $this -> add_check($escrow -> id, 'in', $escrow -> ck3_in_amount, $escrow -> ck3_in_cleared, $escrow -> ck3_in_date, $escrow -> ck3_in_loc, $escrow -> ck3_in_name, $escrow -> ck3_in_number, $escrow -> ck3_in_bounced, $escrow -> ck3_in_cleared_date, '');
            }

            if($escrow -> ck1_out_amount > 0) {
                $this -> add_check($escrow -> id, 'out', $escrow -> ck1_out_amount, $escrow -> ck1_out_cleared, $escrow -> ck1_out_date, $escrow -> ck1_out_loc, $escrow -> ck1_out_name, $escrow -> ck1_out_number, 'no', $escrow -> ck1_out_cleared_date, $escrow -> ck1_out_address);
            }
            if($escrow -> ck2_out_amount > 0) {
                $this -> add_check($escrow -> id, 'out', $escrow -> ck2_out_amount, $escrow -> ck2_out_cleared, $escrow -> ck2_out_date, $escrow -> ck2_out_loc, $escrow -> ck2_out_name, $escrow -> ck2_out_number, 'no', $escrow -> ck2_out_cleared_date, $escrow -> ck2_out_address);
            }
            if($escrow -> ck3_out_amount > 0) {
                $this -> add_check($escrow -> id, 'out', $escrow -> ck3_out_amount, $escrow -> ck3_out_cleared, $escrow -> ck3_out_date, $escrow -> ck3_out_loc, $escrow -> ck3_out_name, $escrow -> ck3_out_number, 'no', $escrow -> ck3_out_cleared_date, $escrow -> ck3_out_address);
            }

        }

        $update_escrow = OldEscrow::whereIn('id', $escrow_ids) -> update(['transferred_to_new_server' => 'yes']);



    }

    public function add_check($escrow_id, $type, $in_amount, $in_cleared, $in_date, $in_loc, $in_name, $in_number, $in_bounced, $in_cleared_date, $out_address) {

        $add_check = new EscrowChecks();
        $add_check -> escrow_id = $escrow_id;
        $add_check -> number = $in_number;
        $add_check -> name = $in_name;
        $add_check -> check_type = $type;
        $add_check -> check_date = $in_date;
        $add_check -> amount = $in_amount;
        $add_check -> cleared = $in_cleared;
        $add_check -> cleared_date = $in_cleared_date;
        $add_check -> bounced = $in_bounced;
        $add_check -> out_address = $out_address;
        $add_check -> url = $in_loc;
        $add_check -> save();

    }

    public function get_checks() {

        $checks = EscrowChecks::where('downloaded', 'no')
        -> with(['escrow', 'escrow.transaction_skyslope:transactionId,mlsNumber,listingGuid,saleGuid', 'escrow.transaction_company:transactionId,mlsNumber,listingGuid,saleGuid'])
        -> inRandomOrder()
        -> limit(100)
        -> get();

        foreach ($checks as $check) {

            $transaction = null;
            if($check -> escrow -> transaction_skyslope) {
                $transaction = $check -> escrow -> transaction_skyslope;
            } else {
                $transaction = $check -> escrow -> transaction_company;
            }

            if($transaction) {

                $listingGuid = '';
                $saleGuid = '';
                if($transaction -> listingGuid != 0) {
                    $listingGuid = $transaction -> listingGuid;
                }
                if($transaction -> saleGuid != 0) {
                    $saleGuid = $transaction -> saleGuid;
                }

                $url = $check -> url;

                if($url) {

                    $file_name = basename($url);
                    $file_contents = file_get_contents($url);

                    $dir = 'doc_management/archives/'.$listingGuid . '_' . $saleGuid;
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    $dir = 'doc_management/archives/'.$listingGuid . '_' . $saleGuid.'/escrow';
                    if(!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    Storage::put($dir.'/'.$file_name, $file_contents);

                    $check -> file_location = $dir.'/'.$file_name;

                }

                $check -> downloaded = 'yes';
                $check -> save();

            }

        }

    } */

}
