<?php

namespace App\Models\DocManagement\Archives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    use HasFactory;

    protected $connection = 'archives';

    protected $table = 'agents';

    protected $primaryKey = 'id';
}
