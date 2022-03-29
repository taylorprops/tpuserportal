<?php

namespace App\Jobs\Cron\BrightMLS;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use App\Models\Temp\AddListings;
use App\Mail\General\EmailGeneral;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Models\BrightMLS\BrightListings;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AddListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this -> onQueue('bright_add_listings');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this -> queueProgress(0);

        ini_set('memory_limit', '-1');

        $dates = AddListings::where('job', 'add_listings') -> first();
        // $start = $dates -> start_date;


        /* if($start > date('Y-m-d')) {
            $message = [
                'company' => 'Taylor Properties',
                'subject' => 'Add listings job has completed',
                'from' => ['email' => 'internal@taylorprops.com', 'name' => 'Taylor Properties'],
                'body' => 'Add listings job has completed',
                'attachments' => null
            ];
            Mail::to(['miketaylor0101@gmail.com'])
            -> send(new EmailGeneral($message));
            return false;
        } */

        // $days = 3;
        // if($start < '2021-01-01') {
        //     $days = '6';
        // }
        // $end = date('Y-m-d', strtotime($start.' +'.$days.' day'));


        $end = $dates -> start_date;
        $start = date('Y-m-d', strtotime($end.' -10 day'));

        $this -> queueData([
            'Start:' => $start,
            'End:' => $end
        ], true);

        $rets = Helper::rets_login();

        if($rets) {

            $resource = "Property";
            $class = "ALL";

            $query = 'MLSListDate='.$start.'-'.$end;

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Select' => 'Appliances, AssociationFee, AssociationFeeFrequency, AssociationYN, AttachedGarageYN, BasementFinishedPercent, BasementYN, BathroomsTotalInteger, BedroomsTotal, BuyerAgentEmail, BuyerAgentFirstName, BuyerAgentFullName, BuyerAgentLastName, BuyerAgentMlsId, BuyerAgentPreferredPhone, BuyerAgentTeamLeadAgentName, BuyerOfficeMlsId, BuyerOfficeName, BuyerTeamName, City, CloseDate, ClosePrice, CondoYN, Cooling, County, ElementarySchool, FireplaceYN, FullStreetAddress, GarageYN, Heating, HighSchool, Latitude, LeaseAmount, ListAgentEmail, ListAgentFirstName, ListAgentLastName, ListAgentMlsId, ListAgentPreferredPhone, ListAgentTeamLeadAgentName, ListingId, ListingKey, ListingSourceRecordKey, ListingTaxID, ListOfficeMlsId, ListOfficeName, ListPictureURL, ListPrice, ListTeamName, LivingArea, Longitude, LotSizeAcres, LotSizeSquareFeet, MajorChangeTimestamp, MiddleOrJuniorSchool, MLSListDate, MlsStatus, NewConstructionYN, Pool, PostalCode, PropertySubType, PropertyType, PublicRemarks, PurchaseContractDate, RealEstateOwnedYN, SaleType, ShortSale, StateOrProvince, StreetDirPrefix, StreetDirSuffix, StreetName, StreetNumber, StreetSuffix, StreetSuffixModifier, StructureDesignType, SubdivisionName, TotalPhotos, UnitBuildingType, UnitNumber, YearBuilt'
                ]
            );


            $listings = $results -> toArray();
            // echo count($listings);
            $this -> queueData(['Found:' => count($listings)], true);

            $increment = 100 / count($listings);
            $progress = 0;
            foreach($listings as $listing) {

                $data = [];
                foreach($listing as $key => $value) {
                    if($value != '') {
                        $data[$key] = $value;
                    }
                }

                BrightListings::firstOrCreate(
                    ['ListingKey' => $listing['ListingKey']],
                    $data
                );

                $progress += $increment;
                $this -> queueProgress($progress);

            }

            $this -> queueProgress(100);

            $rets -> Disconnect();


            $dates -> start_date = $start;
            $dates -> save();

            return true;

        }

        return response() -> json(['failed' => 'login failed']);

    }

}
