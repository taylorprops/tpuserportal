<?php

namespace App\Models\DocManagement\Archives;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;
    use Compoships;

    public $incrementing = false;

    protected $connection = 'archives';

    protected $table = 'documents';

    protected $primaryKey = 'id';

    protected $fillable = ['id'];
}
