<?php

namespace App\Models\DocManagement\Admin\Checklists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminChecklistsItems extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_checklists_items';

}
