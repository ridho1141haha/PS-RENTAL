<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model {
    protected $fillable = ['user_id', 'device_id', 'type', 'status', 'start_time', 'end_time', 'total_price'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function device() {
        return $this->belongsTo(Device::class);
    }
}
