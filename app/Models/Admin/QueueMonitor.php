<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueMonitor extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'queue_monitor';

}
