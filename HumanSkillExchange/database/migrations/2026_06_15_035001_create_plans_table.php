<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('max_skills')->nullable();
            $table->unsignedInteger('max_needs')->nullable();
            $table->unsignedInteger('max_offers')->nullable();
            $table->unsignedInteger('max_exchange_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
