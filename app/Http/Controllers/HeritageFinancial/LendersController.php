<?php

namespace App\Http\Controllers\HeritageFinancial;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\HeritageFinancial\Lenders;
use App\Models\HeritageFinancial\LendersDocuments;
use App\Models\OldDB\Company\Lenders as LendersOld;
use App\Models\DocManagement\Resources\LocationData;

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
        $active = $request -> active ?? 'yes';

        $lenders = Lenders::where(function($query) use ($active) {
            if($active != 'all') {
                $query -> where('active', $active);
            }
        })
        -> where(function($query) use ($search) {
            $query -> where('company_name', 'like', '%'.$search.'%')
                -> orWhere('account_exec_name', 'like', '%'.$search.'%')
                -> orWhere('notes', 'like', '%'.$search.'%');
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

        $states = LocationData::getStates();

        return view('heritage_financial/lenders/view_lender', compact('lender', 'states'));

    }

    public function save_details(Request $request) {

        $request -> validate([
            'company_name' => 'required',
        ],
        [
            'required' => 'Required'
        ]);


        if ($request -> uuid != '') {

            $lender = Lenders::where('uuid', $request -> uuid) -> first() -> update($request -> all());

        } else {

            $lender = new Lenders();
            $uuid = (string) Str::uuid();

            $ignore = ['uuid', 'id'];
            foreach ($request -> all() as $key => $value) {
                if (!in_array($key, $ignore)) {
                    $lender -> $key = $value;
                }
            }
            $lender -> uuid = $uuid;

            $lender -> save();

        }



        return response() -> json([
            'success' => true,
            'uuid' => $lender -> uuid ?? null
        ]);

    }

    public function get_docs(Request $request) {

        $docs = LendersDocuments::where('lender_uuid', $request -> uuid) -> get();

        return compact('docs');

    }

    public function docs_upload(Request $request) {

        $file = $request -> file('lender_docs');
        $uuid = $request -> uuid;

        $file_name_orig = $file -> getClientOriginalName();
        $file_name = Helper::clean_file_name($file, '', false, true);

        $dir = 'mortgage/lenders/docs/'.$uuid;
        if(!is_dir($dir)) {
            Storage::makeDirectory($dir);
        }
        $file -> storeAs($dir, $file_name);
        $file_location = $dir.'/'.$file_name;
        $file_location_url = Storage::url($dir.'/'.$file_name);

        LendersDocuments::create([
            'lender_uuid' => $uuid,
            'file_name' => $file_name_orig,
            'file_location' => $file_location,
            'file_location_url' => $file_location_url,
        ]);



    }

    public function delete_doc(Request $request) {

        $id = $request -> id;

        LendersDocuments::find($id) -> delete();

        return response() -> json(['status' => 'success']);

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

    public function parse_address(Request $request) {

        $lenders = Lenders::get();

        foreach($lenders as $lender) {
            $address = $lender -> account_exec_address;
            if($address != '') {
                $parsed = Helper::parse_address_google($address);
                $lender -> company_street = $parsed['street_number'].' '.$parsed['address'];
                $lender -> company_unit = $parsed['unit'];
                $lender -> company_city = $parsed['city'];
                $lender -> company_state = $parsed['state'];
                $lender -> company_zip = $parsed['zip'];
                //$lender -> save();
            }
        }

    }


}
