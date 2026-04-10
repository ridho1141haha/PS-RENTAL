<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->integer('price_per_hour');
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('devices');
    }
};
