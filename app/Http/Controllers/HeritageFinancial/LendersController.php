<?php

namespace App\Http\Controllers\HeritageFinancial;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Lenders;
use App\Models\OldDB\Company\Lenders as LendersOld;

class LendersController extends Controller
{




    public function lenders(Request $request) {

        return view('heritage_financial/lenders/lenders');

    }

    public function get_lenders(Request $request) {

        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'company_name';
        $length = $request -> length ? $request -> length : 10;

        $search = $request -> search ?? null;
        $active = $request -> active ?? 'Yes';

        $lenders = Lenders::where(function($query) use ($search) {
            $query -> where('company_name', 'like', '%'.$search.'%')
                -> orWhere('account_exec_name', 'like', '%'.$search.'%')
                -> orWhere('notes', 'like', '%'.$search.'%');
        })
        -> where(function($query) use ($active) {
            if($active == 'No') {
                $query -> onlyTrashed();
            } else if($active == '') {
                $query -> withTrashed();
            }
        })
        -> orderBy($sort, $direction)
        -> paginate($length);


        return view('heritage_financial/lenders/get_lenders_html', compact('lenders'));

    }

    public function view_lender(Request $request) {

        $lender = null;
        if($request -> uuid) {
            $lender = Lenders::where('uuid', $request -> uuid) -> first();
        }

        return view('heritage_financial/lenders/view_lender', compact('lender'));

    }

    ////////// Import Loans from Old DB ////////////

    public function import_lenders(Request $request) {

        $lenders = LendersOld::get();


        Lenders::truncate();

        // Add Lenders
        foreach ($lenders as $lender) {

            $add_lender = new Lenders();

            $add_lender -> active = strtolower($lender -> active);
            $add_lender -> company_name = $lender -> company;
            $add_lender -> company_broker_id = $lender -> broker_id;
            $add_lender -> company_sponsor_id = $lender -> sponsor_id;
            $add_lender -> company_website = $lender -> site;
            $add_lender -> company_phone = $lender -> company_phone;
            $add_lender -> company_fax = $lender -> fax;
            $add_lender -> account_exec_name = $lender -> ae_name;
            $add_lender -> account_exec_phone = $lender -> ae_phone;
            $add_lender -> account_exec_email = $lender -> ae_email;
            $add_lender -> account_exec_address = $lender -> ae_address;
            $add_lender -> product_links = $lender -> product_links;
            $add_lender -> fee_sheet_link = $lender -> fee_sheet_link;
            $add_lender -> basis_points = $lender -> basis_points;
            $add_lender -> minimum = $lender -> minimum;
            $add_lender -> maximum = $lender -> maximum;
            $add_lender -> notes = $lender -> notes;

            $add_lender -> save();


        }


    }

    public function add_uuids(Request $request) {

        $lenders = Lenders::get();

        foreach($lenders as $lender) {
            $lender -> uuid = (string) Str::uuid();
            $lender -> save();
        }

    }

}
