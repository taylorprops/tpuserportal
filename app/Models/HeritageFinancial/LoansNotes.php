<?php

namespace App\Models\HeritageFinancial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoansNotes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'heritage_financial_loans_notes';
    protected $guarded = [];

    public function user() {
        return $this -> hasOne(\App\Models\User::class, 'id', 'user_id');
    }

}
