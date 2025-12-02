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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();$table->string('name')->nullable(); // نام اختیاری برای منطقه
            $table->decimal('latitude', 10, 7);  // عرض جغرافیایی
            $table->decimal('longitude', 10, 7); // طول جغرافیایی
            $table->float('radius_km'); // شعاع منطقه به کیلومتر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
