<?php

namespace App\Http\Controllers\HeritageFinancial;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Lenders;
use App\Models\HeritageFinancial\LendersNotes;
use App\Models\OldDB\Company\Lenders as LendersOld;

class LendersController extends Controller
{


    public function lenders(Request $request) {

        return view('heritage_financial/lenders/lenders');

    }

    public function get_lenders(Request $request) {

        $active = $request -> active;

        $lenders = Lenders::where('active', $active) -> get();
        dd($lenders);

        return view('heritage_financial/lenders/get_lenders_html', compact('lenders'));

    }

    ////////// Import Loans from Old DB ////////////

    public function import_lenders(Request $request) {

        $lenders = LendersOld::get();


        Lenders::truncate();
        LendersNotes::truncate();

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

            $add_lender -> save();

            $add_lender_id = $add_lender -> id;

            // add notes
            if ($lender -> notes != '') {
                $user = User::where('email', 'like', '%claure%') -> first();
                $user_id = $user -> id;
                $user_name = $user -> name;

                $add_notes = new LendersNotes();
                $add_notes -> lender_id = $add_lender_id;
                $add_notes -> user_id = $user_id;
                $add_notes -> createdBy = $user_name;
                $add_notes -> notes = $lender -> notes;
            }

        }


    }

}
