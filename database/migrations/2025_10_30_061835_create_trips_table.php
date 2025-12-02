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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date')->default(now());
            $table->string('trip_type')->default('oneway');
            $table->integer('waiting_hours')->nullable();
            $table->boolean('has_pet')->default(false);
            $table->integer('passenger_count')->default(1);
            $table->integer('luggage_count')->default(0);
            $table->json('origins');
            $table->json('destinations');
            $table->foreignId('car_type_id')->constrained('car_types');
            $table->string('trip_time')->nullable();
            $table->string('trip_distance')->nullable();
            $table->decimal('cost', 10, 2);
            $table->text('caption')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('driver_id')->nullable()->constrained('users');
            $table->foreignId('passenger_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
