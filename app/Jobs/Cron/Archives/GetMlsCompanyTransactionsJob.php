<?php

namespace App\Jobs\Cron\Archives;

use App\Models\DocManagement\Archives\Documents as SkySlopeDocuments;
use App\Models\DocManagement\Archives\Transactions as SkySlopeTransactions;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\OldDB\Agents;
use App\Models\OldDB\Company\Transactions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class GetMlsCompanyTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('add_mls_company_transactions');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->get_transactions();
    }

    public function get_transactions()
    {
        $progress = 0;
        $this->queueProgress($progress);

        $transactions = Transactions::select(['ListingSourceRecordId', 'ListingSourceRecordKey', 'ListAgentCompID', 'SaleAgentCompID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode', 'County', 'MlsStatus', 'YearBuilt', 'PropertyType', 'PropertySubType', 'StreetNumber', 'StreetDirPrefix', 'StreetDirSuffix', 'StreetName', 'UnitNumber', 'MLSListDate', 'PurchaseContractDate', 'CloseDate', 'ExpirationDate', 'ListPrice', 'ClosePrice'])
        ->with(['docs'])
        ->where('downloaded', 'no')
        ->inRandomOrder()
        ->limit(500)
        ->get();

        if (count($transactions) > 0) {
            $queue_data = [];

            foreach ($transactions as $transaction) {
                $docs = $transaction->docs;

                //if(count($docs) > 0) {

                $queue_data[] = $transaction->ListingSourceRecordId;

                $add_transaction = new SkySlopeTransactions();

                if ($transaction->ListAgentCompID > 0) {
                    $agent_id = $transaction->ListAgentCompID;
                } else {
                    $agent_id = $transaction->SaleAgentCompID ?? 0;
                }
                $agent_details = $this->agent($agent_id);
                $agent_name = $agent_details['first'].' '.$agent_details['last'];

                $guid = (string) Str::uuid();

                $add_transaction->objectType = $transaction->listing;
                $add_transaction->listingGuid = $guid;
                $add_transaction->status = $transaction->MlsStatus;
                $add_transaction->listingDate = $transaction->MLSListDate;
                $add_transaction->expirationDate = $transaction->ExpirationDate;
                $add_transaction->escrowClosingDate = $transaction->CloseDate;
                $add_transaction->actualClosingDate = $transaction->CloseDate;
                $add_transaction->contractAcceptanceDate = $transaction->PurchaseContractDate;
                $add_transaction->listingPrice = $transaction->ListPrice;
                $add_transaction->salePrice = $transaction->ClosePrice;

                $county = $transaction->County;
                if ($county == '') {
                    $county = $this->county(substr($transaction->PostalCode, 0, 5));
                }
                $county = str_replace(' County', '', $county);
                $county = str_replace("'", '', $county);

                $property = [
                    'zip' => $transaction->PostalCode,
                    'city' => $transaction->City,
                    'unit' => $transaction->UnitNumber,
                    'state' => $transaction->StateOrProvince,
                    'county' => $county,
                    'direction' => $transaction->StreetDirPrefix.$transaction->StreetDirSuffix,
                    'yearBuilt' => $transaction->YearBuilt,
                    'streetNumber' => $transaction->StreetNumber,
                    'streetAddress' => $transaction->StreetName,
                ];
                $add_transaction->property = json_encode($property);
                $add_transaction->address = $transaction->FullStreetAddress;
                $add_transaction->city = $transaction->City;
                $add_transaction->state = $transaction->StateOrProvince;
                $add_transaction->zip = $transaction->PostalCode;

                $agent = [
                    'guid' => '',
                    'publicId' => $agent_id,
                ];
                $add_transaction->agent = json_encode($agent);

                $add_transaction->mlsNumber = $transaction->ListingSourceRecordId ?? '0';
                $add_transaction->data_source = 'mls_company';
                $add_transaction->agentId = $agent_id;
                $add_transaction->agent_name = $agent_name;

                $add_transaction->save();

                if ($add_transaction) {
                    $transaction->downloaded = 'yes';
                    $transaction->save();
                } else {
                    return 'error';
                }

                // add docs to db - downloading done in separate controller
                $dir = 'doc_management/archives/'.$guid;
                if (! Storage::exists($dir)) {
                    Storage::makeDirectory($dir);
                }

                if (count($docs) > 0) {
                    $downloads = [];

                    foreach ($docs as $doc) {
                        if ($doc->invalid != 'yes') {

                                //if(file_exists($doc -> upload_loc)) {

                            $downloads[] = ['from' => $doc->upload_loc, 'to' => $dir.'/'.$doc->upload_file_name];

                            $add_docs = new SkySlopeDocuments();
                            $add_docs->id = (string) Str::uuid();
                            $add_docs->listingGuid = $guid;
                            $add_docs->doc_type = $doc->doc_type;
                            $add_docs->name = str_replace('.pdf', '', $doc->upload_file_name);
                            $add_docs->fileName = $doc->upload_file_name;
                            $add_docs->extension = 'pdf';
                            $add_docs->url = $doc->upload_loc;
                            $add_docs->file_location = $dir.'/'.$doc->upload_file_name;

                            $add_docs->save();

                            //}
                        }
                    }

                    if (count($downloads) > 0) {
                        foreach ($downloads as $download) {
                            if (@fopen($download['from'], 'r')) {
                                Storage::put($download['to'], file_get_contents($download['from']));
                            }
                        }
                    }
                }

                //}

                $progress_increment = round((1 / count($transactions)) * 100);
                $progress += $progress_increment;
                $this->queueProgress($progress);

                $this->queueData(['ListingSourceRecordIds' => $queue_data], true);
            }
        }

        $this->queueProgress(100);
    }

    public function agent($id)
    {
        $agent = Agents::find($id);
        if ($agent) {
            $first = $agent->first;
            $last = $agent->last;
            $email = $agent->email1;
            $phone = $agent->cell_phone;
        } else {
            $first = '';
            $last = '';
            $email = '';
            $phone = '';
        }

        return ['first' => $first, 'last' => $last, 'email' => $email, 'phone' => $phone];
    }

    public function county($zip)
    {
        return LocationData::select('county')->where('zip', $zip)->first()->county;
    }
}
