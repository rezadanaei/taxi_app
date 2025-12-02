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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // نام ماشین
            $table->string('car_identifier', 50)->unique(); // شناسه یا آیدی ماشین
            $table->foreignId('car_type_id') // اشاره به نوع ماشین
                  ->constrained('car_types')
                  ->onDelete('cascade')
                  ->index(); // ایندکس برای سرعت بیشتر

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
