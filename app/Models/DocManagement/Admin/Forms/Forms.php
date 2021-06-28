<?php

namespace App\Models\DocManagement\Admin\Forms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_forms';
    protected $guarded = [];

    public function form_group() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\FormGroups::class, 'id', 'form_group_id');
    }

    public function checklist_group() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ChecklistGroups::class, 'id', 'checklist_group_id');
    }

    public function pages() {
        return $this -> hasMany(\App\Models\DocManagement\Admin\Forms\FormsPages::class, 'form_id', 'id') -> orderBy('page_number');
    }

    public function fields() {
        return $this -> hasMany(\App\Models\DocManagement\Admin\Forms\FormsFields::class, 'form_id', 'id');
    }

}
