<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use TheIconic\NameParser\Parser;
use App\Models\Employees\Mortgage;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use App\Models\DocManagement\Resources\LocationData;

class APIController extends Controller {

    public function test(Request $request) {

        $address = Helper::parse_address_google('8337 Elm Rd Millersville MD 21108');
        $street_number = $address['street_number'] ?? null;
        $street_name = $address['street_name'] ?? null;
        $street_address = $address['address'] ?? null;
        $unit = $address['unit'] ?? null;
        $street = trim($street_number.' '.$street_address);
        $state = $address['state'] ?? null;
        $zip = $address['zip'] ?? null;

        $tax_records = $this -> tax_records($street_number, $street_name, $unit, $zip, null, $state);
        dd($tax_records);

    }

    public function update_loan(Request $request) {

        // verify request is coming from our lending pad account
        $client_id = $request -> client_id;
        if($client_id != 'd7acee3e89454909ae18d06e9a18c077') {
            abort(403);
        }

        $lending_pad_id = $request -> lending_pad_id;
        $loan_number = $request -> loan_number ?? null;

        $street = null;
        $city = null;
        $state = null;
        $zip = null;
        $county = null;

        $borrower_first = null;
        $borrower_last = null;
        $borrower_fullname = null;
        $co_borrower_first = null;
        $co_borrower_last = null;
        $co_borrower_fullname = null;

        $loan_type = $request -> loan_type ?? null;
        $loan_amount =  $request -> loan_amount ? preg_replace('/[\$,]+/', '', $request -> loan_amount) : null;

        $locked = $request -> locked ?? 'None';
        $lock_date = $request -> lock_date ? date('Y-m-d', strtotime($request -> lock_date)) : null;
        $lock_expiration = $request -> lock_expiration ? date('Y-m-d', strtotime($request -> lock_expiration)) : null;

        $loan_officer_1_id = null;
        $processor_id = null;
        $lender_uuid = null;



        // Address
        $address = $request -> address ?? null;
        if($address) {
            $address = Helper::parse_address_google($address);
            $street_number = $address['street_number'] ?? null;
            $street_name = $address['street_name'] ?? null;
            $street_address = $address['address'] ?? null;
            $unit = $address['unit'] ?? null;
            $street = trim($street_number.' '.$street_address);
            if($unit) {
                $street .= ' '.$unit;
            }
            $city = $address['city'] ?? null;
            $state = $address['state'] ?? null;
            $zip = $address['zip'] ?? null;

            if($zip) {
                $zip_lookup = LocationData::where('zip', $zip) -> first();
                $county = $zip_lookup -> county;
            }

        }

        $tax_records = $this -> tax_records($street_number, $street_name, $unit, $zip, null, $state);
        $tax_record_link = null;
        if(isset($tax_records['details']['TaxRecordLink']) && $tax_records['details']['TaxRecordLink'] != '' ){
            $tax_record_link = $tax_records['details']['TaxRecordLink'];
        }


        // Borrowers
        $borrower_fullname = $request -> borrower ?? null;
        if($borrower_fullname) {
            $borrower = $this -> parse_name($borrower_fullname);
            $borrower_first = $borrower['first'].' '.$borrower['middle'];
            $borrower_last = $borrower['last'];
        }
        $co_borrower_fullname = $request -> co_borrower ?? null;
        if($co_borrower_fullname) {
            $co_borrower = $this -> parse_name($co_borrower_fullname);
            $co_borrower_first = $co_borrower['first'].' '.$co_borrower['middle'];
            $co_borrower_last = $co_borrower['last'];
        }

        // People
        $loan_officer = Mortgage::where(function($query) use ($request) {
            $query -> where('email', $request -> loan_officer_email)
            -> orWhere('fullname', $request -> loan_officer)
            -> orWhere('nmls_id', $request -> loan_officer_nmls_id);
        })
        -> first();
        $loan_officer_1_id = $loan_officer -> id;

        if($request -> loan_processor) {
            $processor = Mortgage::where('fullname', $request -> loan_processor) -> first();
            $processor_id = $processor -> id;
        }

        // Lender
        $lender = $request -> lender;
        if($lender) {
            $lender_name = substr($lender, 0, strpos($lender, '(') - 1);
            $lender_short = substr($lender, strpos($lender, '(') + 1, -1);
        }

        $status = 'updated';

        // get loan if it has the lending_pad_id or loan_number
        $loan = Loans::where(function($query) use ($lending_pad_id) {
            $query -> where('lending_pad_id', $lending_pad_id)
            -> whereNotNull('lending_pad_id');
        })
        -> orWhere(function($query) use ($loan_number) {
            $query -> where('loan_number', $loan_number)
            -> whereNotNull('loan_number');
        })
        -> first();


        // if no loan search for matches by address
        if(!$loan) {
            if($street) {
                $street = substr($street, 0, strpos($street, ' ', strpos($street, ' ') + strlen(' ')));
                $loan = Loans::where('street', 'like', '%'.$street.'%') -> first();
            }
            if(!$loan) {
                $loan = Loans::where('borrower_first', $borrower['first'])
                -> where('borrower_last', $borrower_last)
                -> first();
            }

        }


        if(!$loan) {
            // add loan
            $loan = new Loans();
            $status = 'added';
            $loan -> uuid = (string) Str::uuid();
        }


        $loan -> lending_pad_id = $lending_pad_id;

        $loan -> borrower_first = $borrower_first;
        $loan -> borrower_last = $borrower_last;
        $loan -> borrower_fullname = $borrower_fullname;
        $loan -> co_borrower_first = $co_borrower_first;
        $loan -> co_borrower_last = $co_borrower_last;
        $loan -> co_borrower_fullname = $co_borrower_fullname;

        $loan -> street = $street;
        $loan -> city = $city;
        $loan -> state = $state;
        $loan -> county = $county;
        $loan -> zip = $zip;

        $loan -> loan_type = $loan_type;
        $loan -> loan_amount = $loan_amount;
        $loan -> locked = $locked;
        $loan -> lock_date = $lock_date;
        $loan -> lock_expiration = $lock_expiration;
        if($loan_officer_1_id) {
            $loan -> loan_officer_1_id = $loan_officer_1_id;
        }
        if($processor_id) {
            $loan -> processor_id = $processor_id;
        }

        $loan -> save();

        return response() -> json([
            'status', $status,
        ]);

    }

    public function get_critical_dates(Request $request) {

        $lending_pad_id = $request -> loan_id;

        $select = ['time_line_package_to_borrower', 'time_line_sent_to_processing', 'time_line_conditions_received_status', 'time_line_conditions_received', 'time_line_title_ordered', 'time_line_title_received', 'time_line_submitted_to_uw', 'time_line_appraisal_ordered', 'time_line_appraisal_received', 'time_line_voe_ordered', 'time_line_voe_received', 'time_line_conditions_submitted', 'time_line_clear_to_close', 'time_line_scheduled_settlement', 'time_line_closed', 'time_line_scheduled_settlement'];

        $loan = Loans::select($select)
        // -> where('lending_pad_id', $lending_pad_id) -> first()
        -> where('loan_status', 'Closed') -> first();

        $data = new \stdClass();
        foreach($select as $key) {
            if($loan -> $key != '') {
                $data -> $key = date('m/d/Y', strtotime($loan -> $key));
            }
        }

        return response() -> json($data);

    }

    public function parse_name($name) {

        $parser = new Parser();
        $name = $parser -> parse($name);
        $first =  $name -> getFirstname();
        $middle =  $name -> getMiddlename();
        $last =  $name -> getLastname();
        $suffix =  $name -> getSuffix();

        if($suffix) {
            $last .= ', '.$suffix;
        }

        return [
            'first' => $first,
            'middle' => $middle,
            'last' => $last,
            'suffix' => $suffix,
        ];

    }

    public function tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state) {

        $details = [];

        // only able to get tax records for MD at this point
        if ($state == 'MD') {
            if ($tax_id != '') {
                $url = 'https://opendata.maryland.gov/resource/ed4q-f8tm.json?account_id_mdp_field_acctid='.urlencode($tax_id);
            } else {
                $unit_number = '';
                if ($unit != '') {
                    $unit_number = '&mdp_street_address_units_mdp_field_strtunt='.urlencode($unit);
                }
                $url = 'https://opendata.maryland.gov/resource/ed4q-f8tm.json?$where=starts_with%28mdp_street_address_mdp_field_address,%20%27'.$street_number.'%20'.urlencode(strtoupper($street_name)).'%27%29&mdp_street_address_zip_code_mdp_field_zipcode='.$zip.$unit_number;
            }


            $headers = [
                'Content-Type' => 'application/json',
                'X-App-Token' => 'Ya0ATXETWXYaL8teBlGPUbYZ5',
            ];

            $client = new \GuzzleHttp\Client([
                'headers' => $headers
            ]);

            $r = $client -> request('GET', $url);

            $response = $r -> getBody() -> getContents();


            // if tax record found
            if (stristr($response, 'account_id_mdp_field_acctid')) {

                $properties = preg_replace('/\\n/', '', $response);
                $properties = json_decode($response, true);

                if (count($properties) == 1) {

                    $property = $properties[0];

                    $tax_county = str_replace(' County', '', $property['county_name_mdp_field_cntyname']);
                    $tax_county = str_replace('\'', '', $tax_county);

                    $unit_number = '';
                    if(isset($property['premise_address_condominium_unit_no_sdat_field_28'])) {
                        $unit_number = $property['premise_address_condominium_unit_no_sdat_field_28'];
                    } else if(isset($property['mdp_street_address_units_mdp_field_strtunt'])) {
                        $unit_number = $property['mdp_street_address_units_mdp_field_strtunt'];
                    }

                    $details = [
                        'County' => $tax_county ?? null,
                        'ListingTaxID' => $property['account_id_mdp_field_acctid'] ?? null,
                        'StreetNumber' => $property['premise_address_number_mdp_field_premsnum_sdat_field_20'] ?? null,
                        'StreetName' => $property['premise_address_name_mdp_field_premsnam_sdat_field_23'] ?? null,
                        'StreetSuffix' => $property['premise_address_type_mdp_field_premstyp_sdat_field_24'] ?? null,
                        'FullStreetAddress' => $property['mdp_street_address_mdp_field_address'] ?? null,
                        'City' => $property['mdp_street_address_city_mdp_field_city'] ?? null,
                        'PostalCode' => $property['mdp_street_address_zip_code_mdp_field_zipcode'] ?? null,
                        'TaxPropertyType' => $property['land_use_code_mdp_field_lu_desclu_sdat_field_50'] ?? null,
                        'YearBuilt' => $property['c_a_m_a_system_data_year_built_yyyy_mdp_field_yearblt_sdat_field_235'] ?? null,
                        'StateOrProvince' => $state ?? null,
                        'UnitNumber' => $unit_number ?? null,
                        'District' => $property['record_key_district_ward_sdat_field_2'] ?? null,
                        'LegalDescription1' => $property['legal_description_line_1_mdp_field_legal1_sdat_field_17'] ?? null,
                        'TaxRecordLink' => str_replace('http:', 'https:', $property['real_property_search_link']['url']) ?? null,
                        'LegalDescription2' => $property['legal_description_line_2_mdp_field_legal2_sdat_field_18'] ?? null,
                        'LegalDescription3' => $property['legal_description_line_3_mdp_field_legal3_sdat_field_19'] ?? null,
                        'DeedReference1' => $property['deed_reference_1_liber_mdp_field_dr1liber_sdat_field_30'] ?? null,
                        'Deed Reference2' => $property['deed_reference_1_folio_mdp_field_dr1folio_sdat_field_31'] ?? null,
                        'TownCode' => $property['town_code_mdp_field_towncode_desctown_sdat_field_36'] ?? null,
                        'Subdivision Code' => $property['subdivision_code_mdp_field_subdivsn_sdat_field_37'] ?? null,
                        'Map' => $property['map_mdp_field_map_sdat_field_42'] ?? null,
                        'Grid' => $property['grid_mdp_field_grid_sdat_field_43'] ?? null,
                        'Parcel' => $property['parcel_mdp_field_parcel_sdat_field_44'] ?? null,
                        'ResidenceType' => $property['mdp_street_address_type_code_mdp_field_resityp'] ?? null,

                    ];


                    /* if (isset($property['real_property_search_link']['url'])) {

                        // Owner name not available from response so we have to follow the link provided in the results and get the owner's name from that page

                        $link = $property['real_property_search_link']['url'];
                        $link = str_replace('http', 'https', $link);

                        $headers = @get_headers($link);

                        if($headers && strpos( $headers[0], '302')) {
                            $link = str_replace('Location: ', '', $headers[1]);
                            $headers = @get_headers($link);
                        }

                        if($headers && strpos( $headers[0], '200')) {

                            $page = new \DOMDocument();
                            libxml_use_internal_errors(true);
                            $page -> loadHTMLFile($link);
                            $xpath = new \DOMXpath($page);


                            $owner1 = $xpath -> evaluate('string(//span[@id="MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblOwnerName_0"])');

                            if ($owner1 == '') {
                                $owner1 = $xpath -> evaluate('string(//span[@id="MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_ucDetailsSearch_dlstDetaisSearch_lblOwnerName_0"])');
                            }

                            $owner2 = $xpath -> evaluate('string(//span[@id="MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblOwnerName2_0"])');

                            if ($owner2 == '') {
                                $owner2 = $xpath -> evaluate('string(//span[@id="MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_ucDetailsSearch_dlstDetaisSearch_lblOwnerName2_0"])');
                            }

                            $details['Owner1'] = $owner1;
                            $details['Owner2'] = $owner2;


                            // $details['frederick_city'] = 'no';
                            // if(stristr($property['town_code_mdp_field_towncode_desctown_sdat_field_36'], 'Frederick')) { //MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblSpecTaxTown_0
                            //     $details['frederick_city'] = 'yes';
                            // }
                            // $details['condo'] = 'no';
                            // if(stristr($property['land_use_code_mdp_field_lu_desclu_sdat_field_50'], 'condominium')) {
                            //     $details['condo'] = 'yes';
                            // }

                        }


                    } */


                }
            }
        }

        return [
            'details' => $details
        ];

    }



}
