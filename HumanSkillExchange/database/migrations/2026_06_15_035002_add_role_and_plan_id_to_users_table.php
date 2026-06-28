<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('user');
            // is_verified: badge "terverifikasi" yang di-set admin (PRD §14.15 verify user)
            $table->boolean('is_verified')->default(false);
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['role', 'is_verified']);
        });
    }
};
