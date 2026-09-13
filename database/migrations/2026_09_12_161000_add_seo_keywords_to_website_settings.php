<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->text('default_keywords')->nullable()->after('default_meta_description');
        });

        DB::table('website_settings')->update([
            'default_keywords' => 'packing services Nepal, Packers Nepal, household packing Kathmandu, office packing Nepal, fragile item packing, business packing Kathmandu',
        ]);
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn('default_keywords');
        });
    }
};
