<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralStatuses extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_referral_statuses';

    protected $guarded = [];
}
