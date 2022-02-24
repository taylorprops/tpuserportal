<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormGroups extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_form_groups';

    public $timestamps = false;

    protected $guarded = [];

    public function forms()
    {
        return $this->hasMany(\App\Models\DocManagement\Admin\Forms\Forms::class, 'form_group_id')->orderBy('form_name_display', 'asc');
    }
}
