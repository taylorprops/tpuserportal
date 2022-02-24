<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStatuses extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_contract_statuses';

    protected $guarded = [];
}
