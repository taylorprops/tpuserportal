<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCards extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'billing_credit_cards';

    protected $guarded = [];
}
