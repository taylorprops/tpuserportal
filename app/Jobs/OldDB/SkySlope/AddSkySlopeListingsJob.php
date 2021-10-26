<?php

namespace App\Jobs\OldDB\SkySlope;

use App\Helpers\Helper;
use App\Models\OldDB\Agents;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\OldDB\SkySlope\Listings;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\DocManagement\Resources\LocationData;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddSkySlopeListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('add_skyslope_listings');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this -> update_listings();
    }

    public function update_listings() {

        $progress = 0;
        $this -> queueProgress($progress);

        $auth = $this -> skyslope_auth();
        $session = $auth['Session'];

        $headers = [
            'Content-Type' => 'application/json',
            'Session' => $session
        ];

        $modifiedBefore = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-20 day')));
        $modifiedAfter = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime('-15 day')));

        $query = [
            'modifiedAfter' => $modifiedAfter,
            'modifiedBefore' => $modifiedBefore,
            'type' => 'all'
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'query' => $query
        ]);

        $response = $client -> request('GET', 'https://api.skyslope.com/api/files');

        $contents = $response -> getBody() -> getContents();
        $contents = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $contents);
        $contents = json_decode($contents, true);
        $data = $contents['value'];

        $progress_increment = round((1 / count($data)) * 100);

        foreach($data as $transaction) {

            if($transaction['objectType'] != 'summary') {

                $ListingId = $transaction['listingId'] ?? 0;
                $TransactionId = $transaction['transactionId'] ?? 0;

                if($transaction['objectType'] != 'listing') {
                    $add_transaction = Listings::firstOrCreate([
                        'ListingId' => $ListingId,
                        'TransactionId' => $TransactionId
                    ]);
                } else if($transaction['objectType'] != 'sale') {
                    $add_transaction = Listings::firstOrCreate([
                        'TransactionId' => $TransactionId
                    ]);
                    $add_transaction -> ListingId = $ListingId;
                }


                $address = $transaction['property']['streetNumber'];
                if($transaction['property']['direction'] != '') {
                    $address .= ' '.$transaction['property']['direction'];
                }
                $address .= ' '.$transaction['property']['streetAddress'];
                if($transaction['property']['unit'] != '') {
                    $address .= ' '.$transaction['property']['unit'];
                }
                $address .= ' '.$transaction['property']['city'].', '.$transaction['property']['state'].' '.$transaction['property']['zip'];

                $agent = $this -> agent($transaction['agent']['publicId']);
                $agent_first = $agent['first'];
                $agent_last = $agent['last'];
                $agent_email = $agent['email'];
                $agent_phone = $agent['phone'];

                $county = $transaction['property']['county'];
                if($county == '') {
                    $county = $this -> county(substr($transaction['property']['zip'], 0, 5));
                }
                $county = str_replace(' County', '', $county);
                $county = str_replace("'", "", $county);
                $location = $transaction['property']['state'].' - '.$county;

                $deal_type = 'Listing';
                if(isset($transaction['dealType'])) {
                    $deal_type = $transaction['dealType'];
                }

                $add_transaction -> Office_ID = $transaction['officeId'];
                $add_transaction -> STATUS = $transaction['status'];
                $add_transaction -> Stage = $transaction['stage']['name'];
                $add_transaction -> Type_of_Property = $transaction['checklistType'];
                $add_transaction -> Office_ID = $transaction['officeId'];
                $add_transaction -> Type_of_Sale = $deal_type;
                $add_transaction -> MLSNumber = $transaction['mlsNumber'];
                $add_transaction -> Address = $address;
                $add_transaction -> Street_Number = $transaction['property']['streetNumber'];
                $add_transaction -> Street_Dir = $transaction['property']['direction'];
                $add_transaction -> Street_Name = $transaction['property']['streetAddress'];
                $add_transaction -> Unit_Number = $transaction['property']['unit'];
                $add_transaction -> City = $transaction['property']['city'];
                $add_transaction -> State = $transaction['property']['state'];
                $add_transaction -> Zip = $transaction['property']['zip'];

                $add_transaction -> DateEntered = Helper::date_mdy($transaction['createdOn']);
                $add_transaction -> DateEntered_Formatted = substr($transaction['createdOn'], 0, 10);

                if(isset($transaction['listingDate'])) {
                    $add_transaction -> List_Date = Helper::date_mdy($transaction['listingDate']);
                    $add_transaction -> List_Date_Formatted = substr($transaction['listingDate'], 0, 10);
                }

                if(isset($transaction['contractAcceptanceDate'])) {
                    $add_transaction -> Acceptance_Date = Helper::date_mdy($transaction['contractAcceptanceDate']);
                    $add_transaction -> Acceptance_Date_Formatted = substr($transaction['contractAcceptanceDate'], 0, 10);
                }

                if(isset($transaction['escrowClosingDate'])) {
                    $add_transaction -> Scheduled_Close_Date = Helper::date_mdy($transaction['escrowClosingDate']);
                    $add_transaction -> Scheduled_Close_Date_Formatted = substr($transaction['escrowClosingDate'], 0, 10);
                }

                if(isset($transaction['actualClosingDate'])) {
                    $add_transaction -> Actual_Close_Date = Helper::date_mdy($transaction['actualClosingDate']);
                    $add_transaction -> Actual_Close_Date_Formatted = substr($transaction['actualClosingDate'], 0, 10);
                }

                if(isset($transaction['expirationDate'])) {
                    $add_transaction -> Listing_Expiration_Date = Helper::date_mdy($transaction['expirationDate']);
                    $add_transaction -> Listing_Expiration_Date_Formatted = substr($transaction['expirationDate'], 0, 10);
                }

                $add_transaction -> List_Price = $transaction['listingPrice'];
                $add_transaction -> Sale_Price = $transaction['salePrice'] ?? null;

                $add_transaction -> First_Name = $agent_first;
                $add_transaction -> Last_Name = $agent_last;
                $add_transaction -> Email = $agent_email;
                $add_transaction -> Phone = $agent_phone;

                $add_transaction -> Other_First_Name = $transaction['otherSideAgentContact']['firstName'] ?? null;
                $add_transaction -> Other_Last_Name = $transaction['otherSideAgentContact']['lastName'] ?? null;
                $add_transaction -> Other_Email = $transaction['otherSideAgentContact']['email'] ?? null;
                $add_transaction -> Other_Phone = $transaction['otherSideAgentContact']['phoneNumber'] ?? null;
                $add_transaction -> Other_Company = $transaction['otherSideAgentContact']['company'] ?? null;

                $add_transaction -> S1FirstName = $transaction['sellers'][0]['firstName'] ?? null;
                $add_transaction -> S1LastName = $transaction['sellers'][0]['lastName'] ?? null;
                $add_transaction -> S1Company = $transaction['sellers'][0]['company'] ?? null;
                $add_transaction -> S1Email = $transaction['sellers'][0]['email'] ?? null;
                $add_transaction -> S1Phone = $transaction['sellers'][0]['phoneNumber'] ?? null;
                $add_transaction -> S2FirstName = $transaction['sellers'][1]['firstName'] ?? null;
                $add_transaction -> S2LastName = $transaction['sellers'][1]['lastName'] ?? null;
                $add_transaction -> S2Company = $transaction['sellers'][1]['company'] ?? null;
                $add_transaction -> S2Email = $transaction['sellers'][1]['email'] ?? null;
                $add_transaction -> S2Phone = $transaction['sellers'][1]['phoneNumber'] ?? null;

                $add_transaction -> B1FirstName = $transaction['buyers'][0]['firstName'] ?? null;
                $add_transaction -> B1LastName = $transaction['buyers'][0]['lastName'] ?? null;
                $add_transaction -> B1Company = $transaction['buyers'][0]['company'] ?? null;
                $add_transaction -> B1Email = $transaction['buyers'][0]['email'] ?? null;
                $add_transaction -> B1Phone = $transaction['buyers'][0]['phoneNumber'] ?? null;
                $add_transaction -> B2FirstName = $transaction['buyers'][1]['firstName'] ?? null;
                $add_transaction -> B2LastName = $transaction['buyers'][1]['lastName'] ?? null;
                $add_transaction -> B2Company = $transaction['buyers'][1]['company'] ?? null;
                $add_transaction -> B2Email = $transaction['buyers'][1]['email'] ?? null;
                $add_transaction -> B2Phone = $transaction['buyers'][1]['phoneNumber'] ?? null;

                $add_transaction -> Public_ID = $transaction['agent']['publicId'] ?? null;
                $add_transaction -> Office_Name = $location;
                $add_transaction -> County = $county;

                $add_transaction -> save();

                $this -> queueData(['address' => $address], true);

            }

            $progress += $progress_increment;
            $this -> queueProgress($progress);

        }

        $progress = 100;
        $this -> queueProgress($progress);

    }

    function agent($id) {
        $agent = Agents::find($id);
        return ['first' => $agent -> first, 'last' => $agent -> last, 'email' => $agent -> email1, 'phone' => $agent -> cell_phone];
    }

    function county($zip) {
        return LocationData::select('county') -> where('zip', $zip) -> first() -> county;
    }

    public function skyslope_auth() {

        $timestamp = str_replace(' ', 'T', gmdate('Y-m-d H:i:s')).'Z';

        $key = config('global.skyslope_key');
        $client_id = config('global.skyslope_client_id');
        $client_secret = config('global.skyslope_client_secret');
        $secret = config('global.skyslope_secret');

        $str = $client_id.':'.$client_secret.':'.$timestamp;

        $hmac = base64_encode(hash_hmac('sha256', $str, $secret, true));

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'SS '.$key.':'.$hmac,
            'Timestamp' => $timestamp
        ];

        $json = [
            'clientID' => $client_id,
            'clientSecret' => $client_secret
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers,
            'json' => $json
        ]);

        $r = $client -> request('POST', 'https://api.skyslope.com/auth/login');
        $response = $r -> getBody() -> getContents();

        return json_decode($response, true);

    }

}
