<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonFieldsSubGroups extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'docs_resources_common_fields_sub_groups';

    public function common_fields() {
        return $this -> hasMany(\App\Models\DocManagement\Resources\CommonFields::class, 'sub_group_id') -> orderBy('sub_group_id') -> orderBy('field_order');
    }

}
