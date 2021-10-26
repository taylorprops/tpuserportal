<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_agents';
    protected $guarded = [];

    public function docs() {
        return $this -> hasMany(\App\Models\Employees\AgentsDocs::class, 'agent_id');
    }

    public function licenses() {
        return $this -> hasMany(\App\Models\Employees\AgentsLicenses::class, 'agent_id');
    }

    public function user() {
        return $this -> hasMany('App\Models\User', ['user_id', 'group'], ['id', 'emp_type']);
    }

}
