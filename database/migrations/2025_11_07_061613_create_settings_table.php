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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable()->default(null)->comment('نام سایت');
            $table->string('merchant_id', 255)->nullable()->comment('Merchant ID  یا شناسه پذیرنده بانکی');
            $table->string('sms_panel_number')->nullable()->default(null)->comment('شماره پنل پیامک');
            $table->string('sms_panel_username')->nullable()->default(null)->comment('نام کاربری پنل پیامک');
            $table->string('sms_panel_password')->nullable()->default(null)->comment('رمز عبور پنل پیامک');
            $table->string('nashan_web_key')->nullable()->default(null)->comment('کلید وب نشـان');
            $table->string('nashan_service_key')->nullable()->default(null)->comment('کلید سرویس نشـان');
            $table->string('colers_primary', 7)->nullable()->default('#FFA100')->comment('رنگ اصلی سایت');
            $table->string('colers_secondary', 7)->nullable()->default('#FF5E00')->comment('رنگ ثانویه سایت');
            $table->string('colers_tertiary', 7)->nullable()->default('#00C7F4')->comment('رنگ سوم سایت');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
