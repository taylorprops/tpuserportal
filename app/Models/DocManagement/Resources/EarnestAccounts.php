<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarnestAccounts extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_earnest_accounts';

    protected $guarded = [];
}
