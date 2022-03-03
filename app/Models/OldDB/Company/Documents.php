<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';

    protected $table = 'mls_uploads';

    protected $primaryKey = 'upload_id';

    protected $fillable = ['upload_id'];

    public $timestamps = false;
}
