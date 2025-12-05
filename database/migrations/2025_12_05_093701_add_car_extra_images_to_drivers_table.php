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
        Schema::table('drivers', function (Blueprint $table) {
            // چهار طرف خودرو
            $table->string('car_front_image')->nullable()->after('car_insurance');
            $table->string('car_back_image')->nullable()->after('car_front_image');
            $table->string('car_left_image')->nullable()->after('car_back_image');
            $table->string('car_right_image')->nullable()->after('car_left_image');

            // داخل خودرو
            $table->string('car_front_seats_image')->nullable()->after('car_right_image'); 
            $table->string('car_back_seats_image')->nullable()->after('car_front_seats_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'car_front_image',
                'car_back_image',
                'car_left_image',
                'car_right_image',
                'car_front_seats_image',
                'car_back_seats_image'
            ]);
        });
    }
};
