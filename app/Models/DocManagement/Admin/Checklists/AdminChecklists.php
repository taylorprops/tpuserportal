<?php

namespace App\Models\DocManagement\Admin\Checklists;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminChecklists extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_checklists';
    protected $guarded = [];

    public function items() {
        return $this -> hasMany(\App\Models\DocManagement\Admin\Checklists\AdminChecklistsItems::class, 'checklist_id');
    }

    public function location() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ChecklistLocations::class, 'id', 'checklist_location_id');
    }

    public function property_type() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ChecklistPropertyTypes::class, 'id', 'checklist_property_type_id');
    }

    public function property_sub_type() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ChecklistPropertySubTypes::class, 'id', 'checklist_property_sub_type_id');
    }



}
