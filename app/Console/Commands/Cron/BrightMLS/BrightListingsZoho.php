<?php

namespace App\Console\Commands\Cron\BrightMLS;

use App\Models\BrightMLS\BrightListings;
use App\Models\BrightMLS\BrightListingsZoho as Zoho;
use Illuminate\Console\Command;

class BrightListingsZoho extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bright_mls:bright_listings_zoho';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add listings from Bright to Zoho table ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $select = ['ListingId', 'ListingKey', 'MlsStatus', 'BuyerAgentMlsId', 'ClosePrice', 'ListPrice', 'CloseDate', 'MLSListDate', 'City', 'County', 'PostalCode', 'StateOrProvince', 'FullStreetAddress', 'ListAgentMlsId'];

        $data = BrightListings::select($select) -> get() -> toArray();

        Zoho::truncate();
        foreach (array_chunk($data, 1000) as $t) {
            BrightListingsZoho::insert($t);
        }

    }
}
