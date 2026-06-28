<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Kolom social_url lama tetap ada (backward compat)
            // Tambah kolom per platform
            $table->string('github_url')->nullable()->after('social_url');
            $table->string('linkedin_url')->nullable()->after('github_url');
            $table->string('instagram_url')->nullable()->after('linkedin_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('twitter_url');
            $table->string('website_url')->nullable()->after('youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['github_url', 'linkedin_url', 'instagram_url', 'twitter_url', 'youtube_url', 'website_url']);
        });
    }
};
