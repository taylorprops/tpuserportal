<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistLocations extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_checklist_locations';
    public $timestamps = false;
    protected $guarded = [];

}
