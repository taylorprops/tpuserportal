<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTags extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_form_tags';

    public $timestamps = false;

    protected $guarded = [];
}
