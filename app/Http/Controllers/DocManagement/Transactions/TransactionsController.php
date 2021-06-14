<?php

namespace App\Http\Controllers\DocManagement\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BrightMLS\BrightListings;

class TransactionsController extends Controller
{

    public function transactions(Request $request) {

        return view('/doc_management/transactions/transactions');

    }

    public function create(Request $request) {

        $transaction_type = $request -> transaction_type;

        $details = [
            'listing' => [
                'header' => 'Listing',
                'icon' => 'fad fa-sign'
            ],
            'contract' => [
                'header' => 'Sales Contract',
                'icon' => 'fad fa-file-contract'
            ],
            'referral' => [
                'header' => 'Referral Agreement',
                'icon' => 'fad fa-handshake'
            ],
        ][$transaction_type];

        return view('/doc_management/transactions/create', compact('transaction_type', 'details'));

    }

    public function get_property_info(Request $request) {

        $transaction_type = $request -> transaction_type;
        $search_type = $request -> search_type;
        $street_number = $street_name = $unit = $zip = $tax_id = $state = '';
        $ListingId = $request -> ListingId;

        if ($search_type == 'mls') {

            $state = substr($ListingId, 0, 2);

        } else {

            $street_number = $request -> street_number;
            $street_name = $request -> street_name;
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

            $unit = $request -> unit;
            $unit_bright_dmql = '';
            if ($unit != '') {
                $unit_bright_dmql = ',(UnitNumber=*'.$unit.'*)';
            }

            $city = $request -> city;
            $state = $request -> state;
            $zip = $request -> zip;
            $county = $request -> county;


        }

        $select_columns_db = ['ListPictureURL', 'FullStreetAddress', 'City', 'StateOrProvince', 'County', 'PostalCode', 'YearBuilt', 'BathroomsTotalInteger', 'BedroomsTotal', 'MlsStatus', 'ListingId', 'ListPrice', 'PropertyType', 'ListOfficeName', 'MLSListDate', 'ListAgentFirstName', 'ListAgentLastName', 'UnitNumber', 'CloseDate', 'ListingTaxID'];
        $select_columns_bright = 'ListPictureURL, FullStreetAddress, City, StateOrProvince, County, PostalCode, YearBuilt, BathroomsTotalInteger, BedroomsTotal, MlsStatus, ListingId, ListPrice, PropertyType, ListOfficeName, MLSListDate, ListAgentFirstName, ListAgentLastName, UnitNumber, CloseDate, ListingTaxID';

        $property_details = null;
        $results = [];
        $results['multiple'] = false;

        ///// DATABASE SEARCH FOR PROPERTY /////
        if ($search_type == 'mls') {

            $bright_db_search = BrightListings::select($select_columns_db) -> where('ListingId', $ListingId) -> get();
            // address for tax record search
            $street_number = $bright_db_search -> first() -> StreetNumber;
            $street_name = $bright_db_search -> first() -> StreetName;
            $unit = $bright_db_search -> first() -> UnitNumber;
            $zip = $bright_db_search -> first() -> PostalCode;

        } else {

            $bright_db_search = BrightListings::select($select_columns_db)
            -> where('StateOrProvince', $state) -> where('PostalCode', $zip)
            -> where('StreetNumber', $street_number)
            -> where('StreetName', 'LIKE', $street_name.'%')
            -> where('UnitNumber', 'LIKE', '%'.$unit.'%')
            -> whereRaw('((StreetDirSuffix = \''.$street_dir_suffix.'\' or StreetDirSuffix = \''.$street_dir_suffix_alt.'\') or (StreetDirPrefix = \''.$street_dir_suffix.'\' or StreetDirPrefix = \''.$street_dir_suffix_alt.'\'))')
            -> orderBy('MLSListDate', 'DESC')
            -> get();

        }


        // check to see if exists, if multiple and if so return to get unit
        if (count($bright_db_search) > 0) {

            if (count($bright_db_search) > 1) {

                $results['multiple'] = true;
                $property_details = $bright_db_search;


            } else {

                $bright_db_search = $bright_db_search -> first();
                $results['results_bright_id'] = $bright_db_search -> ListingId;

            }

        } else {

            $bright_db_search = null;

        }

        $tax_record_search = [];
        if($state == 'MD') {

            $tax_record_search = $this -> tax_records($street_number, $street_name, $unit, $zip, '', $state);

            // see if more than on result returned
            if($tax_record_search['multiple'] == true) {
                // see if bright results were multiple and return the bigger list
                if($results['multiple'] == true) {
                    if(count($property_details) < count($tax_record_search['details'])) {
                        $property_details = $tax_record_search['details'];
                    }
                    return compact('results', 'property_details');
                } else {
                    $results['multiple'] = true;
                    $property_details = $tax_record_search['details'];
                    return compact('results', 'property_details');
                }

            }

        }

        // if multiple results not returned after tax record search
        if($results['multiple'] == true) {
            return compact('results', 'property_details');
        }


        /******* Final results **********/
        if($tax_record_search) {
            $tax_record_search = $tax_record_search['details'];
        }
        // if found in mls and tax records
        if($bright_db_search && count($tax_record_search) > 0) {

            // keep bright results, add owners from tax records
            $property_details = $bright_db_search;
            $property_details['Owner1'] = null;
            $property_details['Owner2'] = null;

            if (isset($tax_record_search['Owner1']) && $tax_record_search['Owner1'] != '') {
                $property_details['Owner1'] = $tax_record_search['Owner1'] ?? null;
                $property_details['Owner2'] = $tax_record_search['Owner2'] ?? null;
            }

            $property_details['ResidenceType'] = $tax_record_search['ResidenceType'] ?? null;
            if(isset($tax_record_search['TaxRecordLink']) && $tax_record_search['TaxRecordLink'] != '' ){
                $property_details['TaxRecordLink'] = $tax_record_search['TaxRecordLink'];
            }

        // if only found in mls
        } else if($bright_db_search && count($tax_record_search) == 0) {

            $property_details = $bright_db_search;

        // if only found in tax records
        } else if(!$bright_db_search && count($tax_record_search) > 0) {

            $property_details = $tax_record_search;

        }


        return compact('results', 'property_details');

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

                    $details = [
                        'County' => $tax_county ?? null,
                        'ListingTaxID' => $property['account_id_mdp_field_acctid'] ?? null,
                        'Longitude' => $property['mdp_longitude_mdp_field_digxcord_converted_to_wgs84'] ?? null,
                        'Latitude' => $property['mdp_latitude_mdp_field_digycord_converted_to_wgs84'] ?? null,
                        'StreetNumber' => $property['premise_address_number_mdp_field_premsnum_sdat_field_20'] ?? null,
                        'StreetName' => $property['premise_address_name_mdp_field_premsnam_sdat_field_23'] ?? null,
                        'StreetSuffix' => $property['premise_address_type_mdp_field_premstyp_sdat_field_24'] ?? null,
                        'FullStreetAddress' => $property['mdp_street_address_mdp_field_address'] ?? null,
                        'City' => $property['mdp_street_address_city_mdp_field_city'] ?? null,
                        'PostalCode' => $property['mdp_street_address_zip_code_mdp_field_zipcode'] ?? null,
                        'TaxPropertyType' => $property['land_use_code_mdp_field_lu_desclu_sdat_field_50'] ?? null,
                        'YearBuilt' => $property['c_a_m_a_system_data_year_built_yyyy_mdp_field_yearblt_sdat_field_235'] ?? null,
                        'StateOrProvince' => $state ?? null,
                        'UnitNumber' => $property['mdp_street_address_units_mdp_field_strtunt'] ?? null,
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
                        'ZoningCode' => $property['zoning_code_mdp_field_zoning_sdat_field_45'] ?? null,
                        'ResidenceType' => $property['mdp_street_address_type_code_mdp_field_resityp'] ?? null,
                        'UtilitiesWater' => $property['property_factors_utilities_water_mdp_field_pfuw_sdat_field_63'] ?? null,
                        'UtilitiesSewage' => $property['property_factors_utilities_sewer_mdp_field_pfus_sdat_field_64'] ?? null,

                    ];


                    if (isset($property['real_property_search_link']['url'])) {

                        // Owner name not available from response so we have to follow the link provided in the results and get the owner's name from that page

                        try {

                            $link = $property['real_property_search_link']['url'];

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

                        } catch (\Exception $e) {

                            return response() -> json(['error' => $e -> getMessage()]);

                        }

                    }


                } else if(count($properties) > 1) {

                    $filtered_properties = [];
                    foreach($properties as $property) {

                        if($property['mdp_street_address_units_mdp_field_strtunt'] != '') {

                            $filtered_details = [
                                'FullStreetAddress' => $property['mdp_street_address_mdp_field_address'] ?? null,
                                'City' => $property['mdp_street_address_city_mdp_field_city'] ?? null,
                                'PostalCode' => $property['mdp_street_address_zip_code_mdp_field_zipcode'] ?? null,
                                'StateOrProvince' => $state ?? null,
                                'UnitNumber' => str_replace('UNIT ', '', $property['mdp_street_address_units_mdp_field_strtunt']) ?? null
                            ];

                            $filtered_properties[] = $filtered_details;

                        }

                    }

                    return [
                        'multiple' => true,
                        'details' => $filtered_properties
                    ];

                }
            }
        }

        return [
            'multiple' => false,
            'details' => $details
        ];
    }

}
