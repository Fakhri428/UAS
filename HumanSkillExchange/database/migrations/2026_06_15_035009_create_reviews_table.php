<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exchange_request_id')->constrained('exchange_requests')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            // is_hidden: review disembunyikan admin (PRD §14.15 hide review)
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
            $table->unique(['exchange_request_id', 'reviewer_id', 'reviewed_user_id'], 'unique_exchange_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
