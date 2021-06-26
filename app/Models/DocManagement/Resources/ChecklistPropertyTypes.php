<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPropertyTypes extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_checklist_property_types';

    public function checklists() {
        return $this -> hasMany(\App\Models\DocManagement\Admin\Checklists\AdminChecklists::class, 'checklist_property_type_id');
    }

}
