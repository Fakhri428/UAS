<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentoring_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentoring_room_id')->constrained('mentoring_rooms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentoring_bookings');
    }
};
