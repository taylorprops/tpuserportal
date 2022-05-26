<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrightOffices extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'bright_offices';

    protected $guarded = [];

    public function agents()
    {
        return $this -> hasMany(\App\Models\BrightMLS\BrightAgentRoster::class, 'OfficeKey', 'OfficeKey') -> where('active', 'yes') -> orderBy('MemberLastName', 'ASC');
    }
}
