<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_detail')->nullable();
            $table->text('review');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('testimonials')->insert([
            [
                'customer_name' => 'Anita Shrestha',
                'customer_detail' => 'Household packing client · Kathmandu',
                'review' => 'The team made a busy preparation day feel organized. Every box was clear, secure, and ready for collection.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Rajan Karki',
                'customer_detail' => 'Office packing client · Lalitpur',
                'review' => 'Our equipment and documents were packed by area, which made the handover much easier for everyone.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'Mina Gurung',
                'customer_detail' => 'Fragile-item packing client · Bhaktapur',
                'review' => 'They listened carefully, used suitable protection, and labeled every fragile carton clearly. The whole process felt reassuring.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
