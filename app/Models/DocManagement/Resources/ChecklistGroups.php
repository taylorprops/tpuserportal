<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistGroups extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_checklist_groups';

    public $timestamps = false;

    protected $guarded = [];

    public function forms()
    {
        return $this->hasMany(\App\Models\DocManagement\Admin\Forms\Forms::class, 'checklist_group_id');
    }

    public function checklist_items()
    {
        return $this->hasMany(\App\Models\DocManagement\Admin\Checklists\AdminChecklistsItems::class, 'checklist_id');
    }
}
