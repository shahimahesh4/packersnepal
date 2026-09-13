<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('social_title', 100)->nullable();
            $table->string('social_description', 200)->nullable();
            $table->string('social_image')->nullable();
            $table->string('published_canonical_url')->nullable();
            $table->boolean('published_robots_index')->default(true);
            $table->boolean('published_robots_follow')->default(true);
            $table->string('published_social_title', 100)->nullable();
            $table->string('published_social_description', 200)->nullable();
            $table->string('published_social_image')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('social_title', 100)->nullable();
            $table->string('social_description', 200)->nullable();
            $table->string('social_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'robots_index', 'robots_follow', 'social_title', 'social_description', 'social_image', 'published_canonical_url', 'published_robots_index', 'published_robots_follow', 'published_social_title', 'published_social_description', 'published_social_image']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'robots_index', 'robots_follow', 'social_title', 'social_description', 'social_image']);
        });
    }
};
