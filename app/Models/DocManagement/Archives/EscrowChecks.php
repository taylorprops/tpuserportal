<?php

namespace App\Models\DocManagement\Archives;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscrowChecks extends Model
{
    use HasFactory;

    protected $connection = 'archives';
    protected $table = 'escrow_checks';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
