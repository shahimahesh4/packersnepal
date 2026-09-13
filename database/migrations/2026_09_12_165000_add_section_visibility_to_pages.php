<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            foreach (['banner', 'company_story', 'standards', 'distinction', 'cta'] as $section) {
                $table->boolean("show_{$section}")->default(true);
                $table->boolean("published_show_{$section}")->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $columns = [];
            foreach (['banner', 'company_story', 'standards', 'distinction', 'cta'] as $section) {
                $columns[] = "show_{$section}";
                $columns[] = "published_show_{$section}";
            }
            $table->dropColumn($columns);
        });
    }
};
