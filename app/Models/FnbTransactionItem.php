<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FnbTransactionItem extends Model
{
    protected $fillable = ['fnb_transaction_id', 'product_id', 'quantity', 'subtotal'];

    public function transaction() {
        return $this->belongsTo(FnbTransaction::class, 'fnb_transaction_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
