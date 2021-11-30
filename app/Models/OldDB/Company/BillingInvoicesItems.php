<?php

namespace App\Models\OldDB\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingInvoicesItems extends Model
{
    use HasFactory;

    protected $connection = 'mysql_old_company';
    protected $table = 'billing_invoices_items';
    protected $primaryKey = 'in_item_id';
    public $timestamps = false;

    public function invoice() {
        return $this -> belongsTo(\App\Models\OldDB\Company\BillingInvoices::class, 'in_invoice_id', 'in_id');
    }

}
