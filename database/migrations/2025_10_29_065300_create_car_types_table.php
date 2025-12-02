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
        Schema::create('car_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100); // نام دسته (لوکس، اقتصادی، ...)
            $table->text('description')->nullable(); // توضیحات
            $table->decimal('price_per_km', 10, 2)->default(0); // قیمت در هر کیلومتر (مثلاً 2500.00)
            $table->string('header_image')->nullable(); // تصویر هدر (مسیر فایل)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_types');
    }
};
