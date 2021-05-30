<?php

namespace App\Models\DocManagement\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonFields extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_common_fields';

}
