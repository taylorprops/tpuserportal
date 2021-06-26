<?php

namespace App\Models\DocManagement\Admin\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormsPages extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_forms_pages';

}
