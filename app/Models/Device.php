<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model {
    protected $fillable = ['name', 'type', 'price_per_hour', 'status'];

    public function rentals() {
        return $this->hasMany(Rental::class);
    }
}
