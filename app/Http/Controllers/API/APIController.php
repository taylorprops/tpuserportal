<?php

namespace App\Http\Controllers\API;

use App\Classes\DatabaseChangeLog;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Employees\Mortgage;
use App\Models\HeritageFinancial\Lenders;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TheIconic\NameParser\Parser;

class APIController extends Controller
{


    public function update_loan(Request $request)
    {

        // verify request is coming from our lending pad account
        $client_id = $request -> client_id;
        if($client_id != 'd7acee3e89454909ae18d06e9a18c077') {
            return response() -> json(['status' => 'success']);
        }

        $loan_status_detailed = $request -> loan_status;
        $ignore_statuses = ['Lead', 'Prospect', 'Pre Qualify', 'Pre Approval', 'Application Taken', 'Registered', 'Broker Initial Submission'];

        if (! in_array($loan_status_detailed, $ignore_statuses)) {
            $action = $request -> action ?? null;
            $loan_id = $request -> loan_id ?? null;

            $lending_pad_uuid = $request -> lending_pad_uuid;
            $lending_pad_loan_number = $request -> lending_pad_loan_number;
            $loan_number = $request -> loan_number ?? null;

            $statuses_open = ['Processing', 'Initial Submission', 'Approved', 'Suspended', 'Pre Deny', 'Broker Condition Submission', 'Condition Submission', 'Clear To Close'];
            $statuses_cancelled = ['Denied', 'Withdrawn', 'Not Accepted', 'Incomplete', 'Rescinded'];
            $statuses_closed = ['Closed', 'Funded', 'Post Closing', 'In Shipping', 'Purchased', 'Servicing'];

            $loan_status = '';
            if (in_array($loan_status_detailed, $statuses_open)) {
                $loan_status = 'Open';
            } elseif (in_array($loan_status_detailed, $statuses_cancelled)) {
                $loan_status = 'Cancelled';
            } elseif (in_array($loan_status_detailed, $statuses_closed)) {
                $loan_status = 'Closed';
            }

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
            $loan_purpose = $request -> loan_purpose ?? null;
            $loan_amount = $request -> loan_amount ? preg_replace('/[\$,]+/', '', $request -> loan_amount) : null;

            $locked = $request -> locked ?? 'None';
            $lock_date = $request -> lock_date ? date('Y-m-d', strtotime($request -> lock_date)) : null;
            $lock_expiration = $request -> lock_expiration ? date('Y-m-d', strtotime($request -> lock_expiration)) : null;

            $loan_officer_1_id = null;
            $processor_id = null;
            $lender_uuid = null;

            $title_company = preg_match('/(.*)\n/', $request -> title_company, $matches) ? $matches[1] : null;
            $agent_company_seller = preg_match('/(.*)\n/', $request -> agent_company_seller, $matches) ? $matches[1] : null;
            $agent_name_seller = preg_match('/.*\n(.*)\n/', $request -> agent_company_seller, $matches) ? $matches[1] : null;
            $agent_company_buyer = preg_match('/(.*)\n/', $request -> agent_company_buyer, $matches) ? $matches[1] : null;
            $agent_name_buyer = preg_match('/.*\n(.*)\n/', $request -> agent_company_buyer, $matches) ? $matches[1] : null;

            // Address
            $address = $request -> address ?? null;
            $update_address = $request -> update_address;
            $tax_record_link = null;
            $property_full_details = null;

            if ($update_address == 'yes' || $action == 'add' || $action == 'match') {
                $address = Helper::parse_address_google($address);
                $street_number = $address['street_number'] ?? null;
                $street_name = $address['street_name'] ?? null;
                $street_address = $address['address'] ?? null;
                $unit = $address['unit'] ?? null;
                $street = trim($street_number.' '.$street_address);
                if ($unit) {
                    $street .= ' '.$unit;
                }
                $city = $address['city'] ?? null;
                $state = $address['state'] ?? null;
                $zip = $address['zip'] ?? null;

                if ($zip) {
                    $zip_lookup = LocationData::where('zip', $zip) -> first();
                    $county = $zip_lookup -> county;
                }

                $tax_records = $this -> tax_records($street_number, $street_name, $unit, $zip, null, $state);
                $property_full_details = $tax_records['details'];
                if (isset($tax_records['details']['TaxRecordLink']) && $tax_records['details']['TaxRecordLink'] != '') {
                    $tax_record_link = $tax_records['details']['TaxRecordLink'];
                }
            }

            // Borrowers
            $borrower_fullname = $request -> borrower ?? null;
            if ($borrower_fullname) {
                $borrower = $this -> parse_name($borrower_fullname);
                $borrower_first = $borrower['first'];
                if ($borrower['middle'] != '') {
                    $borrower_first .= ' '.$borrower['middle'];
                }
                $borrower_last = $borrower['last'];
            }

            $co_borrower_fullname = $request -> co_borrower ?? null;
            if ($co_borrower_fullname != null) {
                $co_borrower = $this -> parse_name($co_borrower_fullname);
                $co_borrower_first = $co_borrower['first'];
                if ($co_borrower['middle'] != '') {
                    $co_borrower_first .= ' '.$co_borrower['middle'];
                }
                $co_borrower_last = $co_borrower['last'];
            }

            // People
            $loan_officer = Mortgage::where(function ($query) use ($request) {
                $query -> where('email', $request -> loan_officer_email)
                -> orWhere('fullname', $request -> loan_officer)
                -> orWhere('nmls_id', $request -> loan_officer_nmls_id);
            })
            -> first();
            $loan_officer_1_id = $loan_officer -> id;

            if ($request -> loan_processor) {
                $processor = Mortgage::where('fullname', $request -> loan_processor) -> first();
                $processor_id = $processor -> id;
            }

            // Lender
            $lender = $request -> lender;
            if ($lender) {
                $lender_name = substr($lender, 0, strpos($lender, '(') - 1);
                $lender_short = substr($lender, strpos($lender, '(') + 1, -1);

                $lender_search = Lenders::where('company_name', $lender_name)
                -> orWhere('company_name_short', $lender_short)
                -> get();

                if (count($lender_search) == 1) {
                    $lender_uuid = $lender_search -> first() -> uuid;
                }
            }

            $status = 'updated';
            $loan = null;

            if ($action && $action == 'match') {
                $loan = Loans::with(['loan_officer_1']) -> find($loan_id);
                $status = 'matched';
            } else if($action && $action == 'add') {
                // add loan
                $loan = new Loans();
                $status = 'added';
                $loan -> uuid = (string) Str::uuid();
                $db_log_data_before = null;
            }

            if (! $loan) {

                // get loan if it has the lending_pad_loan_number
                $loan = Loans::where(function ($query) use ($lending_pad_loan_number) {
                    $query -> where('lending_pad_loan_number', $lending_pad_loan_number)
                    -> whereNotNull('lending_pad_loan_number')
                    -> where('lending_pad_loan_number', '!=', '');
                })
                -> with(['loan_officer_1'])
                -> first();
            }

            // if no loan search for matches by address and borrower
            if (! $loan) {
                $loans = null;

                $cut_off_date = '2021-08-01';

                $address = $request -> address;
                $street = null;
                if ($address) {
                    $street = preg_match('/(.*)\n/', $request -> address, $matches) ? $matches[1] : null;
                    $street = substr($street, 0, strpos($street, ' ', strpos($street, ' ') + strlen(' ')));
                }
                $borrower_first = $borrower['first'];

                $loans = Loans::select('*', DB::raw('DATE_FORMAT(settlement_date,"%m/%d/%Y") as settle_date'))
                -> where(function ($query) use ($street, $borrower_first, $borrower_last) {
                    $query -> where(function ($query) use ($borrower_first, $borrower_last) {
                        $query -> where('borrower_first', $borrower_first)
                        -> where('borrower_last', $borrower_last);
                    })
                    -> orWhere(function ($query) use ($street) {
                        if ($street) {
                            $query -> where('street', 'like', '%'.$street.'%');
                        }
                    });
                })
                -> where(function ($query) {
                    $query -> whereNull('lending_pad_loan_number')
                    -> orWhere('lending_pad_loan_number', '');
                })
                -> where(function($query) use ($cut_off_date) {
                    $query -> whereNull('settlement_date')
                    -> orWhere('settlement_date', '>', $cut_off_date);
                })
                -> with(['loan_officer_1'])
                -> get();

                if (count($loans) > 0) {
                    return response() -> json([
                        'status' => 'found',
                        'loans' => $loans,
                    ]);
                }
            }

            if (! $loan && (! $action || $action == 'retry')) {
                return response() -> json([
                    'status' => 'not_found',
                ]);
            }

            if ($loan) {
                $db_log_data_before = $loan -> replicate();
            }



            $loan -> lending_pad_uuid = $lending_pad_uuid;
            $loan -> lending_pad_loan_number = $lending_pad_loan_number;
            $loan -> loan_number = $loan_number;

            $loan -> loan_status = $loan_status;
            $loan -> loan_status_detailed = $loan_status_detailed;

            $loan -> borrower_first = $borrower_first;
            $loan -> borrower_last = $borrower_last;
            $loan -> borrower_fullname = $borrower_fullname;
            $loan -> co_borrower_first = $co_borrower_first;
            $loan -> co_borrower_last = $co_borrower_last;
            $loan -> co_borrower_fullname = $co_borrower_fullname;

            if($update_address == 'yes' || $action == 'add' || $action == 'match') {
                $loan -> street = $street;
                $loan -> city = $city;
                $loan -> state = $state;
                $loan -> county = $county;
                $loan -> zip = $zip;
                $loan -> tax_record_link = $tax_record_link;
                $loan -> property_details = json_encode($property_full_details);
            }

            $loan -> loan_type = $loan_type;
            $loan -> loan_purpose = $loan_purpose;
            $loan -> loan_amount = $loan_amount;
            $loan -> locked = $locked;
            $loan -> lock_date = $lock_date;
            $loan -> lock_expiration = $lock_expiration;
            if ($loan_officer_1_id) {
                $loan -> loan_officer_1_id = $loan_officer_1_id;
            }
            if ($processor_id) {
                $loan -> processor_id = $processor_id;
            }
            $loan -> lender_uuid = $lender_uuid;
            $loan -> title_company = $title_company;
            $loan -> agent_company_seller = $agent_company_seller;
            $loan -> agent_name_seller = $agent_name_seller;
            $loan -> agent_company_buyer = $agent_company_buyer;
            $loan -> agent_name_buyer = $agent_name_buyer;

            if ($request -> Funded) {
                $loan -> time_line_funded = date('Y-m-d', strtotime($request -> Funded));
            }
            $settlement_date = null;
            $loan -> time_line_scheduled_settlement = null;
            $loan -> time_line_estimated_settlement = null;
            if($request -> Estimate_Closing != null) {
                $loan -> time_line_estimated_settlement = date('Y-m-d', strtotime($request -> Estimate_Closing));
            }
            if($request -> Schedule_Closing != null) {
                $loan -> time_line_scheduled_settlement = date('Y-m-d', strtotime($request -> Schedule_Closing));
                $settlement_date = date('Y-m-d', strtotime($request -> Schedule_Closing));
            }
            if($request -> Closed) {
                $loan -> time_line_closed = date('Y-m-d', strtotime($request -> Closed));
                $settlement_date = date('Y-m-d', strtotime($request -> Closed));
            }
            $loan -> settlement_date = $settlement_date;

            if($request -> Processing) {
                $loan -> time_line_sent_to_processing = date('Y-m-d', strtotime($request -> Processing));
            }
            if ($request -> Clear_To_Close) {
                $loan -> time_line_clear_to_close = date('Y-m-d', strtotime($request -> Clear_To_Close));
            }
            if ($request -> Condition_Submission) {
                $loan -> time_line_conditions_submitted = date('Y-m-d', strtotime($request -> Condition_Submission));
            }
            if ($request -> Approved) {
                $loan -> time_line_conditions_received_status = 'approved';
            }
            if ($request -> Suspended) {
                $loan -> time_line_conditions_received_status = 'suspended';
            }
            if ($request -> Appraisal_Delivered) {
                $loan -> time_line_appraisal_received = date('Y-m-d', strtotime($request -> Appraisal_Delivered));
            }


            $loan -> save();

            $tax_record_link = $loan -> tax_record_link;
            $property_full_details = $loan -> property_details;

            $db_log_data_after = Loans::find($loan -> id);
            $changed_by = auth() -> user() -> id ?? 'system';
            $model = 'Loans';
            $model_id = $loan -> id;

            $db_log = new DatabaseChangeLog();
            $db_log -> log_changes($changed_by, $model, $model_id, $db_log_data_before, $db_log_data_after);

            return response() -> json([
                'status' => $status,
                'action' => $action,
                'tax_record_link' => $tax_record_link,
                'property_full_details' => json_decode($property_full_details),
            ]);
        }

        return response() -> json(['status' => 'not_added']);
    }

    public function parse_name($name)
    {
        $parser = new Parser();
        $name = $parser -> parse($name);
        $first = $name -> getFirstname();
        $middle = $name -> getMiddlename();
        $last = $name -> getLastname();
        $suffix = $name -> getSuffix();

        if ($suffix) {
            $last .= ', '.$suffix;
        }

        return [
            'first' => $first,
            'middle' => $middle,
            'last' => $last,
            'suffix' => $suffix,
        ];
    }

    public function tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state)
    {
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
                'headers' => $headers,
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
                    if (isset($property['premise_address_condominium_unit_no_sdat_field_28'])) {
                        $unit_number = $property['premise_address_condominium_unit_no_sdat_field_28'];
                    } elseif (isset($property['mdp_street_address_units_mdp_field_strtunt'])) {
                        $unit_number = $property['mdp_street_address_units_mdp_field_strtunt'];
                    }

                    $details = [
                        'ResidenceType' => $property['mdp_street_address_type_code_mdp_field_resityp'] ?? null,
                        'TransferDate' => str_replace('.', '-', $property['sales_segment_1_transfer_date_yyyy_mm_dd_mdp_field_tradate_sdat_field_89']) ?? null,
                        'OriginalCost' => '$'.number_format($property['sales_segment_1_consideration_mdp_field_considr1_sdat_field_90'], 2) ?? null,
                        'NumberOfUnits' => $property['c_a_m_a_system_data_number_of_dwelling_units_mdp_field_bldg_units_sdat_field_239'] ?? null,
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
                        'TaxRecordLink' => str_replace('http:', 'https:', $property['real_property_search_link']['url']) ?? null,
                        'LegalDescription1' => $property['legal_description_line_1_mdp_field_legal1_sdat_field_17'] ?? null,
                        'LegalDescription2' => $property['legal_description_line_2_mdp_field_legal2_sdat_field_18'] ?? null,
                        'LegalDescription3' => $property['legal_description_line_3_mdp_field_legal3_sdat_field_19'] ?? null,
                        'DeedReference1' => $property['deed_reference_1_liber_mdp_field_dr1liber_sdat_field_30'] ?? null,
                        'Deed Reference2' => $property['deed_reference_1_folio_mdp_field_dr1folio_sdat_field_31'] ?? null,
                        'TownCode' => $property['town_code_mdp_field_towncode_desctown_sdat_field_36'] ?? null,
                        'Subdivision Code' => $property['subdivision_code_mdp_field_subdivsn_sdat_field_37'] ?? null,
                        'Map' => $property['map_mdp_field_map_sdat_field_42'] ?? null,
                        'Grid' => $property['grid_mdp_field_grid_sdat_field_43'] ?? null,
                        'Parcel' => $property['parcel_mdp_field_parcel_sdat_field_44'] ?? null,
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
            'details' => $details,
        ];
    }

    /* from taylorprops.com */
    public function submit_recruiting_form(Request $request) {

        return 'working';

    }
}
