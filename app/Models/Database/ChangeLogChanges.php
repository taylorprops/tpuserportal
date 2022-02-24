<?php

namespace App\Models\Database;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeLogChanges extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'database_change_log_changes';

    protected $guarded = [];

    public function loan_officer_1_before()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_before');
    }

    public function loan_officer_1_after()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_after');
    }

    public function loan_officer_2_before()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_before');
    }

    public function loan_officer_2_after()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_after');
    }

    public function processor_before()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_before');
    }

    public function processor_after()
    {
        return $this->hasOne(\App\Models\Employees\Mortgage::class, 'id', 'value_after');
    }

    public function lender_before()
    {
        return $this->hasOne(\App\Models\HeritageFinancial\Lenders::class, 'uuid', 'value_before');
    }

    public function lender_after()
    {
        return $this->hasOne(\App\Models\HeritageFinancial\Lenders::class, 'uuid', 'value_after');
    }
}
