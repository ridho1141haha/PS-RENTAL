<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['fixed', 'open_bill'])->default('fixed');
            $table->enum('status', ['unpaid', 'active', 'completed', 'cancelled'])->default('unpaid');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('total_price')->default(0);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('rentals');
    }
};
