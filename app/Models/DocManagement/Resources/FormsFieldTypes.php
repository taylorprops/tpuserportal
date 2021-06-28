<?php

namespace App\Models\DocManagement\Resources\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormsFieldTypes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_forms_field_types';
    protected $guarded = [];

}
