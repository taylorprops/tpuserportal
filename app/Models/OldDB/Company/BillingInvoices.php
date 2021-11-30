<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoices extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';
    protected $table = 'billing_invoices';
    protected $primaryKey = 'in_id';
    public $timestamps = false;

    public function items() {
        return $this -> hasMany(\App\Models\OldDB\Company\BillingInvoicesItems::class, 'in_invoice_id', 'in_id');
    }

}
