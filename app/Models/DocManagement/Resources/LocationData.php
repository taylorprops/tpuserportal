<?php

namespace App\Models\DocManagement\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationData extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'docs_resources_location_data';

    public $guarded = [];

    public function scopeGetStates()
    {
        return $this -> select('state') -> distinct() -> orderBy('state') -> get();
    }

    public function scopeActiveStates()
    {
        return config('global.taylor_properties_active_states');
    }
}
