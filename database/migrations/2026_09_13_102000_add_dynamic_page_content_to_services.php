<?php

use App\Support\ServicePageDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            $table->string('cta_image')->nullable();
            $table->json('page_content')->nullable();
            $table->json('section_visibility')->nullable();
        });

        DB::table('services')->update([
            'page_content' => json_encode(ServicePageDefaults::content()),
            'section_visibility' => json_encode(ServicePageDefaults::visibility()),
        ]);
    }

    public function down(): void
    {
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn(['banner_image', 'cta_image', 'page_content', 'section_visibility']));
    }
};
