<?php

namespace App\Models\DocManagement\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormsFieldsInputs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_forms_fields_inputs';

}
