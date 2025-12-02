<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
        $table->id();

        // اطلاعات شخصی
        $table->string('first_name', 100)->nullable();
        $table->string('last_name', 100)->nullable();
        $table->string('father_name', 100)->nullable();
        $table->date('birth_date')->nullable();
        $table->string('national_code', 20)->nullable();
        $table->text('address')->nullable();

        // تصاویر مدارک هویتی
        $table->string('id_card_front')->nullable();
        $table->string('id_card_back')->nullable();
        $table->string('id_card_selfie')->nullable();
        $table->string('profile_photo')->nullable();

        // اطلاعات رانندگی
        $table->string('license_number', 50)->nullable();
        $table->string('license_front')->nullable();
        $table->string('license_back')->nullable();

        // اطلاعات خودرو
        $table->foreignId('car_id')
            ->nullable()
            ->constrained('cars')
            ->onDelete('cascade'); // بدون index() اضافه
        $table->string('car_type', 100)->nullable();
        $table->string('car_plate', 20)->nullable();
        $table->string('car_model', 100)->nullable();
        $table->string('car_card_front')->nullable();
        $table->string('car_card_back')->nullable();
        $table->string('car_insurance')->nullable();
        

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
}
