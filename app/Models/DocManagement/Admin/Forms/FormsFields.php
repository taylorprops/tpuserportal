<?php

namespace App\Models\DocManagement\Admin\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormsFields extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_forms_fields';

}
