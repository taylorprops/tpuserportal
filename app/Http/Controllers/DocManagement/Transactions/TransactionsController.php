<?php

namespace App\Http\Controllers\DocManagement\Transactions;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\BrightMLS\BrightListings;
use App\Models\CRM\CRMContacts;
use App\Models\DocManagement\Resources\ChecklistLocations;
use App\Models\DocManagement\Resources\ChecklistPropertySubTypes;
use App\Models\DocManagement\Resources\ChecklistPropertyTypes;
use App\Models\DocManagement\Resources\FormGroups;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\DocManagement\Transactions\Transactions;
use App\Models\Employees\Agents;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionsController extends Controller
{
    public function transactions(Request $request)
    {
        return view('/doc_management/transactions/transactions');
    }

    public function create(Request $request)
    {
        $transaction_type = $request->transaction_type;

        $details = [
            'listing' => [
                'header' => 'Listing',
                'icon' => 'fad fa-sign',
            ],
            'contract' => [
                'header' => 'Sales Contract',
                'icon' => 'fad fa-file-contract',
            ],
            'referral' => [
                'header' => 'Referral Agreement',
                'icon' => 'fad fa-handshake',
            ],
        ][$transaction_type];

        $states = LocationData::select('state')->groupBy('state')->orderBy('state')->get();
        $agents = Agents::select(['id', 'first_name', 'last_name'])->where('active', 'yes')->orderBy('last_name')->get();

        return view('/doc_management/transactions/create', compact('transaction_type', 'details', 'states', 'agents'));
    }

    public function save_transaction(Request $request)
    {
        $transaction_type = $request->transaction_type;
        $for_sale = $request->for_sale;
        $checklist_details = json_decode($request->checklist_details, true);
        $property_details = json_decode($request->property_details, true);
        $required_details = json_decode($request->required_details, true);
        $sellers = json_decode($request->sellers, true);
        $buyers = json_decode($request->buyers, true) ?? null;

        $transaction = new Transactions();
        $transaction->transaction_type = $transaction_type;

        if ($transaction_type == 'listing') {
            $validator = $request->validate([
                'ListPrice' => 'required',
                'MLSListDate' => 'required',
                'ExpirationDate' => 'required',
            ],
            [
                'required' => 'Required Field',
            ]);

            $Listing_ID = 1;
            if (Transactions::max('Listing_ID')) {
                $Listing_ID = Transactions::max('Listing_ID') + 1;
            }
            $transaction->Listing_ID = $Listing_ID;
        }

        if ($transaction_type == 'contract') {
            $validator = $request->validate([
                'ContractPrice' => 'required_if:for_sale,==,yes',
                'LeaseAmount' => 'required_if:for_sale,==,no',
                'ListAgentFirstName' => 'required',
                'ListAgentLastName' => 'required',
                'ListOfficeName' => 'required',
                'ListAgentPreferredPhone'=> 'required',
                'ListAgentMlsId' => 'required',
                'ContractDate' => 'required_if:for_sale,==,yes',
                'CloseDate' => 'required',
                'UsingHeritageTitle' => 'required_if:for_sale,==,yes',
            ],
            [
                'required' => 'Required Field',
                'required_if' => 'Required Field',
            ]);

            $Contract_ID = 1;
            if (Transactions::max('Contract_ID')) {
                $Contract_ID = Transactions::max('Contract_ID') + 1;
            }
            $transaction->Contract_ID = $Contract_ID;
        }

        if ($transaction_type == 'referral') {
            $validator = $request->validate([
                'ReferralClientFirstName' => 'required',
                'ReferralClientLastName' => 'required',
                'ReferralClientPhone' => 'required',
                'ReferralClientStreet'=> 'required',
                'ReferralClientZip' => 'required',
                'ReferralClientCity' => 'required',
                'ReferralClientState' => 'required',
                'ReferralReceivingAgentFirstName' => 'required',
                'ReferralReceivingAgentLastName' => 'required',
                'ReferralReceivingAgentOfficeName' => 'required',
                'ReferralReceivingAgentOfficeStreet' => 'required',
                'ReferralReceivingAgentOfficeCity' => 'required',
                'ReferralReceivingAgentOfficeState' => 'required',
                'ReferralSettlementDate' => 'required',
                'ReferralCommissionAmount' => 'required',
                'ReferralReferralPercentage' => 'required',
                'ReferralAgentCommission' => 'required',
            ],
            [
                'required' => 'Required Field',
            ]);

            $Referral_ID = 1;
            if (Transactions::max('Referral_ID')) {
                $Referral_ID = Transactions::max('Referral_ID') + 1;
            }
            $transaction->Referral_ID = $Referral_ID;
        }

        foreach ($property_details as $key => $val) {
            // convert dates
            if (preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $val)) {
                $val = date('Y-m-d', strtotime($val));
            } elseif (preg_match('/\$[0-9]/', $val)) {
                // convert money
                $val = preg_replace('/[\$,]+/', '', $val);
            }
            $transaction->$key = $val;
        }
        foreach ($required_details as $key => $val) {
            if (preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $val)) {
                $val = date('Y-m-d', strtotime($val));
            } elseif (preg_match('/\$[0-9]/', $val)) {
                $val = preg_replace('/[\$,]+/', '', $val);
            }
            if ($key == 'ListPrice' || $key == 'ContractPrice' || $key == 'LeaseAmount') {
                $val = $val == '' ? '0' : $val;
            }
            $transaction->$key = $val;
        }

        foreach ($checklist_details as $key => $val) {
            $transaction->$key = $val;
        }

        $transaction->save();

        // add members

        // DO NOT add members to transaction
        // remove buyer and seller, list agent and buyer agent fields from transaction table

        // add Location_ID
        // add checklist

        // if listing
        // update listing office details to ours
        // update listing agent details

        // if a contract
        // update contract office details to ours
        // update buyer agent details
        // add commission and add Commission_ID to transaction
        // add earnest and add Earnest_ID to transaction

        // create property email

        // set Status
    }

    public function get_contacts(Request $request)
    {
        $contacts = CRMContacts::where('user_id', auth()->user()->id)->orderBy('contact_last')->get();

        return datatables()->of($contacts)
        ->addColumn('import', function ($contacts) {
            return '<button class="button primary sm"
                data-id="'.$contacts->id.'"
                data-first="'.$contacts->contact_first.'"
                data-last="'.$contacts->contact_last.'"
                data-company="'.$contacts->contact_company.'"
                data-phone_cell="'.$contacts->contact_phone_cell.'"
                data-email="'.$contacts->contact_email.'"
                data-street="'.$contacts->contact_street.'"
                data-city="'.$contacts->contact_city.'"
                data-state="'.$contacts->contact_state.'"
                data-zip="'.$contacts->contact_zip.'"
                x-on:click="import_contact($el)"
                ><i class="fal fa-check fa-sm mr-2"></i> Import</button>';
        })
        ->editColumn('contact_first', function ($contacts) {
            return $contacts->contact_last.', '.$contacts->contact_first;
        })
        ->escapeColumns([])
        ->make(true);
    }

    public function validate_form_manual_entry(Request $request)
    {
        $validator = $request->validate([
            'street_number' => 'required',
            'street_name' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
        ],
        [
            'required' => 'Required Field',
        ]);
    }

    public function validate_form_checklist_details(Request $request)
    {
        $validator = $request->validate([
            'Agent_ID' => 'required',
            'SaleRent' => 'required',
            'PropertyType' => 'required',
            'PropertySubType' => 'required_if:for_sale,==,yes',
            'YearBuilt' => 'required_if:show_disclosures,==,yes',  //|integer|digits:4|max:'.date('Y', strtotime('+1 year'))
            'HoaCondoFees' => 'required_if:show_disclosures,==,yes',
        ],
        [
            'required' => 'Required Field',
            'required_if' => 'Required Field',
        ]);
    }

    public function get_property_info(Request $request)
    {
        $transaction_type = $request->transaction_type;
        $search_type = $request->search_type;
        $street_number = $street_name = $unit = $zip = $tax_id = $state = '';
        $ListingId = $request->ListingId;

        if ($search_type == 'mls') {
            $state = substr($ListingId, 0, 2);
        } else {
            $street_number = $request->street_number;
            $street_name = $request->street_name;
            $street_suffix = '';
            $street_dir_suffix = '';
            $street_dir_suffix_alt = '';

            // remove all suffixes and dir suffixes to get just street name. Save them for later
            $street_suffixes_array = ['ALLEY', 'AVENUE', 'BEND', 'BOULEVARD', 'BRANCH', 'CIRCLE', 'CIR', 'CORNER', 'COURSE', 'COURT', 'COVE', 'CRESCENT', 'CROSSING', 'DRIVE', 'DRIVEWAY', 'EXTENSION', 'GARDENS', 'GARTH', 'GATEWAY', 'GLEN', 'GROVE', 'HARBOR', 'HIGHWAY', 'HILL', 'HOLLOW', 'KNOLLS', 'LANDING', 'LANE', 'LOOP', 'MEWS', 'MILLS', 'NORTHWAY', 'PARKWAY', 'PASSAGE', 'PATH', 'PIKE', 'PLACE', 'RIDGE', 'ROAD', 'ROUTE', 'ROW', 'RUN', 'SQUARE', 'STREET', 'TERRACE', 'TRACE', 'TRAIL', 'TURN', 'VIEW', 'VISTA', 'WALK', 'WAY'];
            $street_dir_suffixes_array = ['E', 'EAST', 'N', 'NE', 'NORTH', 'NORTHEAST', 'NORTHWEST', 'NW', 'S', 'SE', 'SOUTH', 'SOUTHEAST', 'SOUTHWEST', 'SW', 'W', 'WEST'];

            $street_dir_suffixes_alt_array = [
                ['orig' => 'E', 'alt' => 'EAST'],
                ['orig' => 'EAST', 'alt' => 'E'],
                ['orig' => 'W', 'alt' => 'WEST'],
                ['orig' => 'WEST', 'alt' => 'W'],
                ['orig' => 'S', 'alt' => 'SOUTH'],
                ['orig' => 'SOUTH', 'alt' => 'S'],
                ['orig' => 'N', 'alt' => 'NORTH'],
                ['orig' => 'NORTH', 'alt' => 'N'],
                ['orig' => 'NE', 'alt' => 'NORTHEAST'],
                ['orig' => 'NORTHEAST', 'alt' => 'NE'],
                ['orig' => 'NW', 'alt' => 'NORTHWEST'],
                ['orig' => 'NORTHWEST', 'alt' => 'NW'],
                ['orig' => 'SE', 'alt' => 'SOUTHEAST'],
                ['orig' => 'SOUTHEAST', 'alt' => 'SE'],
                ['orig' => 'SW', 'alt' => 'SOUTHWEST'],
                ['orig' => 'SOUTHWEST', 'alt' => 'SW'],
            ];

            foreach ($street_suffixes_array as $street_suffixes) {
                if (preg_match('/\s\b('.$street_suffixes.'(?!.*'.$street_suffixes.'))\b/i', $street_name, $matches)) {
                    $street_name = preg_replace('/\\s\b('.$street_suffixes.'(?!.*'.$street_suffixes.'))\b/i', '', $street_name);
                    $street_suffix = trim($matches[0]);
                }
            }

            foreach ($street_dir_suffixes_array as $street_dir_suffixes) {

                // do not pull prefixes i.e. 234 SW some st
                if (! preg_match('/[0-9]+\s('.$street_dir_suffixes.')/i', $street_name)) {

                    // only pull suffixes i.e. 234 Main St. Southwest
                    if (preg_match('/\s\b('.$street_dir_suffixes.')\b/i', $street_name, $matches)) {
                        $street_name = preg_replace('/\s\b('.$street_dir_suffixes.')\b/i', '', $street_name);
                        $street_dir_suffix = trim($matches[0]);

                        foreach ($street_dir_suffixes_alt_array as $dir) {
                            if (strtolower($dir['orig']) == strtolower($street_dir_suffix)) {
                                $street_dir_suffix_alt = $dir['alt'];
                            }
                        }
                    }
                }
            }

            $street_dir_suffix_bright_dmql = '';
            if ($street_dir_suffix != '') {
                $street_dir_suffix_bright_dmql = ',(((StreetDirSuffix=|'.$street_dir_suffix.')|(StreetDirSuffix=|'.$street_dir_suffix_alt.'))|((StreetDirPrefix=|'.$street_dir_suffix.')|(StreetDirPrefix=|'.$street_dir_suffix_alt.')))';
            }

            $unit = $request->unit;
            $unit_bright_dmql = '';
            if ($unit != '') {
                $unit_bright_dmql = ',(UnitNumber=*'.$unit.'*)';
            }

            $city = $request->city;
            $state = $request->state;
            $zip = $request->zip;
            $county = $request->county;
        }

        $select_columns_bright = config('global.select_columns_bright');

        $property_details = null;
        $results = [];
        $results['multiple'] = false;

        ///// DATABASE SEARCH FOR PROPERTY /////
        if ($search_type == 'mls') {
            $bright_db_search = BrightListings::select($select_columns_bright)->where('ListingId', $ListingId)->first();

            if (! $bright_db_search) {
                return response()->json(['error' => 'not found']);
            }

            // address for tax record search
            $street_number = $bright_db_search->StreetNumber;
            $street_name = $bright_db_search->StreetName;
            $unit = $bright_db_search->UnitNumber;
            $zip = $bright_db_search->PostalCode;
        } else {
            $bright_db_search = BrightListings::select($select_columns_bright)
            ->where('StateOrProvince', $state)->where('PostalCode', $zip)
            ->where('StreetNumber', $street_number)
            ->where('StreetName', $street_name)
            ->where(function ($query) use ($unit) {
                if ($unit != '') {
                    $query->where('UnitNumber', $unit);
                }
            })
            ->whereRaw('((StreetDirSuffix = \''.$street_dir_suffix.'\' or StreetDirSuffix = \''.$street_dir_suffix_alt.'\') or (StreetDirPrefix = \''.$street_dir_suffix.'\' or StreetDirPrefix = \''.$street_dir_suffix_alt.'\'))')
            ->orderBy('MLSListDate', 'DESC')
            ->get();

            if (count($bright_db_search) != 1) {
                $bright_db_search = null;
            } else {
                $bright_db_search = $bright_db_search->first();
            }
        }

        // check to see if exists
        if ($bright_db_search) {
            $bright_db_search->MLSListDate = Helper::date_mdy($bright_db_search->MLSListDate);
            $bright_db_search->CloseDate = Helper::date_mdy($bright_db_search->CloseDate);
            $results['results_bright_id'] = $bright_db_search->ListingId;
        }

        $tax_record_search = null;
        if ($state == 'MD') {
            $tax_record_search = $this->tax_records($street_number, $street_name, $unit, $zip, '', $state);

            if (isset($tax_record_search['error'])) {
                $tax_record_search = null;
            }
        }

        /* %%%%%%%%%% Final results %%%%%%%%%% */
        if ($tax_record_search) {
            $tax_record_search = $tax_record_search['details'];
        }
        // if found in mls and tax records
        if ($bright_db_search && $tax_record_search) {

            // keep bright results, add owners from tax records
            $property_details = $bright_db_search;
            $property_details['Owner1'] = null;
            $property_details['Owner2'] = null;

            if (isset($tax_record_search['Owner1']) && $tax_record_search['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }

            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            if (isset($tax_record_search['TaxRecordLink']) && $tax_record_search['TaxRecordLink'] != '') {
                $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'];
            }

            // if only found in mls
        } elseif ($bright_db_search && ! $tax_record_search) {
            $property_details = $bright_db_search;

        // if only found in tax records
        } elseif (! $bright_db_search && $tax_record_search) {
            $property_details = $tax_record_search;
        }

        return compact('results', 'property_details');
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

            $r = $client->request('GET', $url);

            $response = $r->getBody()->getContents();

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
            'details' => $details,
        ];
    }

    public function get_counties(Request $request)
    {
        $counties = LocationData::select('county')
        ->where('state', $request->state)
        ->groupBy('county')
        ->orderBy('county')
        ->get()
        ->map(function ($field) {
            $field['county'] = ucwords(strtolower($field['county']));

            return $field;
        })
        ->pluck('county');

        return $counties;
    }

    public function get_location_details(Request $request)
    {
        $location = LocationData::where('zip', $request->zip)->get()
        ->map(function ($field) {
            $field['city'] = ucwords(strtolower($field['city']));
            $field['county'] = ucwords(strtolower($field['county']));

            return $field;
        });

        return $location->first();
    }

    public function get_property_types(Request $request)
    {
        return ChecklistPropertyTypes::select(['id', 'property_type'])
        ->orderBy('display_order')
        ->get();
    }

    public function get_property_sub_types(Request $request)
    {
        return ChecklistPropertySubTypes::select(['id', 'property_sub_type'])
        ->orderBy('display_order')
        ->get();
    }

    public function get_locations(Request $request)
    {
        return ChecklistLocations::select(['id', 'location', 'state'])
        ->orderBy('display_order')
        ->get();
    }

    public function get_form_groups(Request $request)
    {
        return FormGroups::select(['id', 'group_name'])
        ->get();
    }

    public function agent_search(Request $request)
    {
        $val = $request->val;

        return BrightAgentRoster::select(['MemberKey', 'MemberFirstName', 'MemberLastName', 'MemberPreferredPhone', 'OfficePhone', 'MemberEmail', 'MemberMlsId', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode', 'MemberType', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
        ->where('MemberLastName', 'like', '%'.$val.'%')
            ->orWhere('MemberEmail', 'like', '%'.$val.'%')
            ->orWhere('MemberMlsId', 'like', '%'.$val.'%')
            ->orWhereRaw('CONCAT(MemberFirstName, " ", MemberLastName) like \'%'.$val.'%\'')
            ->orWhereRaw('CONCAT(MemberNickname, " ", MemberLastName) like \'%'.$val.'%\'')
            ->orderBy('MemberLastName')
            ->limit(50)
            ->get();
    }
}
