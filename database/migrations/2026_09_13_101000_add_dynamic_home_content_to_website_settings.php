<?php

use App\Support\HomePageDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->json('home_content')->nullable();
            $table->json('home_visibility')->nullable();
        });
        DB::table('website_settings')->update([
            'home_content' => json_encode(HomePageDefaults::content()),
            'home_visibility' => json_encode(HomePageDefaults::visibility()),
        ]);
    }

    public function down(): void
    {
        Schema::table('website_settings', fn (Blueprint $table) => $table->dropColumn(['home_content', 'home_visibility']));
    }
};
