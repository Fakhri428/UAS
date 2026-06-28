<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('category', 100);
            $table->text('description');
            $table->text('exchange_offer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
