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
        Schema::table('mentoring_rooms', function (Blueprint $table) {
            $table->string('video_link')->nullable()->after('price');
            $table->string('meeting_notes')->nullable()->after('video_link');
        });

        Schema::table('mentoring_bookings', function (Blueprint $table) {
            $table->enum('session_status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentoring_rooms', function (Blueprint $table) {
            $table->dropColumn(['video_link', 'meeting_notes']);
        });

        Schema::table('mentoring_bookings', function (Blueprint $table) {
            $table->dropColumn('session_status');
        });
    }
};
