<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsignCallbacks extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'esign_callbacks';

    protected $guarded = [];
}
