<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTypes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_contact_types';

    protected $guarded = [];
}
