<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentoring_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 180);
            $table->text('description');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('price')->default(0);
            $table->dateTime('schedule')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentoring_rooms');
    }
};
