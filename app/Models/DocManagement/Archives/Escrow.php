<?php

namespace App\Models\DocManagement\Archives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    use HasFactory;

    protected $connection = 'archives';
    protected $table = 'escrow';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    public function checks() {
        return $this -> hasMany(\App\Models\DocManagement\Archives\EscrowChecks::class, 'escrow_id', 'id');
    }

}
