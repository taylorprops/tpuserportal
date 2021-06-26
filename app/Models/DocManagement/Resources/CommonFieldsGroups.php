<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonFieldsGroups extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_common_fields_groups';

    public function common_fields() {
        return $this -> hasMany(\App\Models\DocManagement\Resources\CommonFields::class, 'group_id') -> orderBy('group_id') -> orderBy('field_order');
    }

    public function sub_groups() {
        return $this -> hasMany(\App\Models\DocManagement\Resources\CommonFieldsSubGroups::class, 'group_id') -> orderBy('group_id') -> orderBy('sub_group_order');
    }

}
