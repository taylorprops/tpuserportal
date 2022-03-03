<?php

namespace App\Http\Controllers\DocManagement\Archives;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Archives\Escrow;
use App\Models\DocManagement\Archives\EscrowChecks;
use App\Models\DocManagement\Archives\Transactions;
use App\Models\OldDB\Company\Escrow as OldEscrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EscrowController extends Controller
{
    public function escrow(Request $request)
    {
        return view('doc_management/transactions/archived/escrow');
    }

    public function get_escrow(Request $request)
    {
        $direction = $request->direction ? $request->direction : 'desc';
        $sort = $request->sort ? $request->sort : 'contract_date';
        $length = $request->length ? $request->length : 10;
        $active = $request->active;

        $select = [
            'escrow.id',
            'mls',
            'TransactionId',
            'contract_date',
            'agent',
            'address',
            'city',
            'state',
            'zip',
        ];

        $search = $request->search ?? null;
        $escrows = Escrow::select($select)
        ->where(function ($query) use ($search) {
            if ($search) {
                $query->where('address', 'like', '%'.$search.'%')
                ->orWhere('agent', 'like', '%'.$search.'%');
            }
        })
        /* -> where(function($query) use ($active) {
            if ($active != 'all') {
                $query -> whereHas('checks', function($query) {
                    $query -> select(DB::raw('sum(case when cleared = "yes" and amount > "0" and check_type = "in" then amount else 0 end) as checks_in, sum(case when cleared = "yes" and amount > "0" and check_type = "out" then amount else 0 end) as checks_out'))
                    -> havingRaw('checks_in > checks_out')
                    -> groupBy('escrow_id');
                    // $query -> having(DB::raw('
                    // sum(case
                    //     when cleared = "yes" and amount > "0" and check_type = "in" then amount else 0 end) >
                    // sum(case
                    //     when cleared = "yes" and amount > "0" and check_type = "out" then amount else 0 end)'));
                });
            }
        }) */
        ->with(['transaction_skyslope:transactionId,mlsNumber,listingGuid,saleGuid,actualClosingDate,escrowClosingDate', 'transaction_company:transactionId,mlsNumber,listingGuid,saleGuid,actualClosingDate,escrowClosingDate', 'checks'])
        ->orderBy($sort, $direction);

        if ($request->to_excel == 'false') {
            $escrows = $escrows->paginate($length);

            return view('doc_management/transactions/archived/get_escrow_html', compact('escrows'));
        } else {
            $escrows = $escrows->get();

            $data = [];
            $select = ['Address', 'Agent', 'Close Date', 'Money In', 'Money Out', 'Holding'];

            foreach ($escrows as $escrow) {
                $address = $escrow->address.' '.$escrow->city.', '.$escrow->state.' '.$escrow->zip;
                if ($escrow->mls != '') {
                    $transaction = $escrow->transaction_company;
                } else {
                    $transaction = $escrow->transaction_skyslope;
                }
                $close_date = null;
                if ($transaction) {
                    $close_date = substr($transaction->escrowClosingDate, 0, 10);
                    if ($transaction->actualClosingDate != '') {
                        $close_date = substr($transaction->actualClosingDate, 0, 10);
                    }
                }
                if (! $close_date) {
                    $close_date = $escrow->contract_date;
                }

                $checks = $escrow->checks;

                $escrow_total_in = $checks->where('cleared', 'yes')
                ->where('amount', '>', '0')
                ->where('check_type', 'in')
                ->sum('amount');

                $escrow_total_out = $checks->where('cleared', 'yes')
                ->where('amount', '>', '0')
                ->where('check_type', 'out')
                ->sum('amount');

                $escrow_total_left = $escrow_total_in - $escrow_total_out;

                $escrow_total_in = '$'.number_format($escrow_total_in, 0);
                $escrow_total_out = '$'.number_format($escrow_total_out, 0);
                $escrow_total_left = '$'.number_format($escrow_total_left, 0);

                $data[] = [
                    'address' => $address,
                    'agent' => $escrow->agent,
                    'close_date' => $close_date,
                    'escrow_total_in' => $escrow_total_in,
                    'escrow_total_out' => $escrow_total_out,
                    'escrow_total_left' => $escrow_total_left,
                ];
            }

            $filename = 'escrows_'.time().'.xlsx';
            $file = Helper::to_excel($data, $filename, $select);

            return response()->json(['file' => $file]);
        }
    }

    //////// TRANSFER ARCHIVE /////////

    public function get_old_escrow(Request $request)
    {

        // TODO: update old server transferred_to_new_server = no

        $escrows = OldEscrow::limit(1000)->where('transferred_to_new_server', 'no')->get();

        $escrow_ids = [];

        foreach ($escrows as $escrow) {
            $escrow_ids[] = $escrow->id;

            $add_escrow = Escrow::firstOrCreate(['id' => $escrow->id]);

            foreach ($escrow->toArray() as $key => $value) {
                if ($value == '0000-00-00') {
                    $value = '';
                }

                $add_escrow->$key = $value;
            }

            $add_escrow->save();

            if ($escrow->ck1_in_amount > 0) {
                $this->add_check($escrow->id, 'in', $escrow->ck1_in_amount, $escrow->ck1_in_cleared, $escrow->ck1_in_date, $escrow->ck1_in_loc, $escrow->ck1_in_name, $escrow->ck1_in_number, $escrow->ck1_in_bounced, $escrow->ck1_in_cleared_date, '');
            }
            if ($escrow->ck2_in_amount > 0) {
                $this->add_check($escrow->id, 'in', $escrow->ck2_in_amount, $escrow->ck2_in_cleared, $escrow->ck2_in_date, $escrow->ck2_in_loc, $escrow->ck2_in_name, $escrow->ck2_in_number, $escrow->ck2_in_bounced, $escrow->ck2_in_cleared_date, '');
            }
            if ($escrow->ck3_in_amount > 0) {
                $this->add_check($escrow->id, 'in', $escrow->ck3_in_amount, $escrow->ck3_in_cleared, $escrow->ck3_in_date, $escrow->ck3_in_loc, $escrow->ck3_in_name, $escrow->ck3_in_number, $escrow->ck3_in_bounced, $escrow->ck3_in_cleared_date, '');
            }

            if ($escrow->ck1_out_amount > 0) {
                $this->add_check($escrow->id, 'out', $escrow->ck1_out_amount, $escrow->ck1_out_cleared, $escrow->ck1_out_date, $escrow->ck1_out_loc, $escrow->ck1_out_name, $escrow->ck1_out_number, 'no', $escrow->ck1_out_cleared_date, $escrow->ck1_out_address);
            }
            if ($escrow->ck2_out_amount > 0) {
                $this->add_check($escrow->id, 'out', $escrow->ck2_out_amount, $escrow->ck2_out_cleared, $escrow->ck2_out_date, $escrow->ck2_out_loc, $escrow->ck2_out_name, $escrow->ck2_out_number, 'no', $escrow->ck2_out_cleared_date, $escrow->ck2_out_address);
            }
            if ($escrow->ck3_out_amount > 0) {
                $this->add_check($escrow->id, 'out', $escrow->ck3_out_amount, $escrow->ck3_out_cleared, $escrow->ck3_out_date, $escrow->ck3_out_loc, $escrow->ck3_out_name, $escrow->ck3_out_number, 'no', $escrow->ck3_out_cleared_date, $escrow->ck3_out_address);
            }
        }

        $update_escrow = OldEscrow::whereIn('id', $escrow_ids)->update(['transferred_to_new_server' => 'yes']);
    }

    public function update_old_escrow(Request $request)
    {
        $escrows = Escrow::with(['checks'])->get();
        $ids = [];
        foreach ($escrows as $escrow) {
            $checks = $escrow->checks;

            $escrow_total_in = $checks->where('cleared', 'yes')
            ->where('amount', '>', '0')
            ->where('check_type', 'in')
            ->sum('amount');

            $escrow_total_out = $checks->where('cleared', 'yes')
            ->where('amount', '>', '0')
            ->where('check_type', 'out')
            ->sum('amount');

            $escrow_total_left = $escrow_total_in - $escrow_total_out;

            if ($escrow_total_left > 0) {
                $ids[] = $escrow->id;
            }
        }

        dump($ids);
    }

    public function add_check($escrow_id, $type, $in_amount, $in_cleared, $in_date, $in_loc, $in_name, $in_number, $in_bounced, $in_cleared_date, $out_address)
    {
        $add_check = new EscrowChecks();
        $add_check->escrow_id = $escrow_id;
        $add_check->number = $in_number;
        $add_check->name = $in_name;
        $add_check->check_type = $type;
        $add_check->check_date = $in_date;
        $add_check->amount = $in_amount;
        $add_check->cleared = $in_cleared;
        $add_check->cleared_date = $in_cleared_date;
        $add_check->bounced = $in_bounced;
        $add_check->out_address = $out_address;
        $add_check->url = $in_loc;
        $add_check->save();
    }

    public function get_checks()
    {
        $checks = EscrowChecks::where('downloaded', 'no')
        ->with(['escrow', 'escrow.transaction_skyslope:transactionId,mlsNumber,listingGuid,saleGuid', 'escrow.transaction_company:transactionId,mlsNumber,listingGuid,saleGuid'])
        ->inRandomOrder()
        ->limit(100)
        ->get();

        foreach ($checks as $check) {
            $transaction = null;
            if ($check->escrow->transaction_skyslope) {
                $transaction = $check->escrow->transaction_skyslope;
            } else {
                $transaction = $check->escrow->transaction_company;
            }

            if ($transaction) {
                $listingGuid = '';
                $saleGuid = '';
                if ($transaction->listingGuid != 0) {
                    $listingGuid = $transaction->listingGuid;
                }
                if ($transaction->saleGuid != 0) {
                    $saleGuid = $transaction->saleGuid;
                }

                $url = $check->url;

                if ($url) {
                    $file_name = basename($url);
                    $file_contents = file_get_contents($url);

                    $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid;
                    if (! Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    $dir = 'doc_management/archives/'.$listingGuid.'_'.$saleGuid.'/escrow';
                    if (! Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    Storage::put($dir.'/'.$file_name, $file_contents);

                    $check->file_location = $dir.'/'.$file_name;
                }

                $check->downloaded = 'yes';
                $check->save();
            }
        }
    }

    public function add_guids(Request $request)
    {
        $before = Escrow::whereNull('listingGuid')->whereNotNull('mls')->count();

        $escrows = Escrow::whereNull('listingGuid')->whereNotNull('mls')->limit(1000)->get();

        foreach ($escrows as $escrow) {
            $transaction = Transactions::where('mlsNumber', $escrow->mls)->first();
            if ($transaction) {
                $escrow->listingGuid = $transaction->listingGuid;
                $escrow->saleGuid = $transaction->saleGuid;
                $escrow->save();
            }
        }

        $after = Escrow::whereNull('listingGuid')->whereNotNull('mls')->count();

        dump($before, $after);
    }
}
