<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyListingsNotes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'company_listings_notes';

    protected $guarded = [];
}
