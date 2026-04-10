<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FnbTransaction extends Model
{
    protected $fillable = ['user_id', 'status', 'guest_name', 'total_price', 'payment_method'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(FnbTransactionItem::class);
    }
}
