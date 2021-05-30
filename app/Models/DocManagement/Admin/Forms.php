<?php

namespace App\Models\DocManagement\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_forms';

    public function form_group() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\FormGroups::class, 'id', 'form_group_id');
    }

    public function checklist_group() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ChecklistGroups::class, 'id', 'checklist_group_id');
    }

}
