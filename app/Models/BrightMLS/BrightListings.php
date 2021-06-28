<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrightListings extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'bright_listings';
    protected $guarded = [];

}
