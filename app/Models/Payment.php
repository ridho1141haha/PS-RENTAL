<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['rental_id', 'amount', 'payment_method', 'status', 'proof_of_payment'];

    public function rental() {
        return $this->belongsTo(Rental::class);
    }
}
