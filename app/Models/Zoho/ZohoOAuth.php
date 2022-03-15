<?php

namespace App\Models\Zoho;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoOAuth extends Model
{
    use HasFactory;

    protected $table = 'zohooauth';
    public $timestamps = false;

}
