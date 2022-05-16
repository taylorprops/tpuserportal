<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCenterAddresses extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'marketing_test_center_addresses';

    protected $guarded = [];
}
