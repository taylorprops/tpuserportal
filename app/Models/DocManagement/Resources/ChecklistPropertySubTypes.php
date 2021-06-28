<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPropertySubTypes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_checklist_property_sub_types';
    protected $guarded = [];

}
