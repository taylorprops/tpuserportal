<?php

namespace App\Models\OldDB\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrightAgents extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_bright';

    protected $table = 'bright_agent_roster';

    protected $primaryKey = 'MemberKey';
}
