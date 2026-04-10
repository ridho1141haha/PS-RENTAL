<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fnb_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name')->nullable(); // Boleh diisi atau kosong
            $table->integer('total_price')->default(0);
            $table->string('payment_method')->nullable(); // Cash, QRIS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fnb_transactions');
    }
};
