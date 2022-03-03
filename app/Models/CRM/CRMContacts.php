<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMContacts extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'crm_contacts';

    protected $guarded = [];
}
