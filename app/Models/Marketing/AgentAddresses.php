<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentAddresses extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'marketing_agent_addresses';
    protected $guarded = [];

}
