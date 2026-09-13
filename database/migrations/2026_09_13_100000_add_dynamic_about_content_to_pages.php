<?php

use App\Support\AboutPageDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('about_content')->nullable();
            $table->json('published_about_content')->nullable();
        });

        $content = AboutPageDefaults::content();
        DB::table('pages')->where('slug', 'about')->update([
            'about_content' => json_encode($content),
            'published_about_content' => json_encode($content),
        ]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['about_content', 'published_about_content']);
        });
    }
};
