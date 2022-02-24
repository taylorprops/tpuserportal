<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanOfficerAddresses extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'marketing_loan_officer_addresses';

    protected $guarded = [];
}
