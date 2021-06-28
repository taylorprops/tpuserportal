<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminResources extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'admin_resource_items';
    protected $guarded = [];

}
