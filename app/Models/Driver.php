<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Storage;

class Driver extends Model
{
    /**
     * Mass assignable.
     */
    protected $fillable = [
        'first_name', 'last_name', 'father_name', 'birth_date', 'national_code', 'address',
        'id_card_front', 'id_card_back', 'id_card_selfie', 'profile_photo',
        'license_number', 'license_front', 'license_back',
        'car_id', 'car_type', 'car_plate', 'car_model', 'car_card_front', 'car_card_back', 'car_insurance',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relations
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }


    /**
     * Booted events for deleting old files
     */
    protected static function booted()
    {
        // 1️⃣ حذف فایل‌ها هنگام حذف رکورد
        static::deleting(function ($driver) {

            $files = [
                'id_card_front',
                'id_card_back',
                'id_card_selfie',
                'profile_photo',
                'license_front',
                'license_back',
                'car_card_front',
                'car_card_back',
                'car_insurance',
            ];

            foreach ($files as $file) {
                if ($driver->$file && Storage::disk('public')->exists($driver->$file)) {
                    Storage::disk('public')->delete($driver->$file);
                }
            }
        });


        // 2️⃣ حذف فایل قبلی هنگام آپدیت رکورد
        static::updating(function ($driver) {

            $fields = [
                'id_card_front',
                'id_card_back',
                'id_card_selfie',
                'profile_photo',
                'license_front',
                'license_back',
                'car_card_front',
                'car_card_back',
                'car_insurance',
            ];

            foreach ($fields as $field) {

                if ($driver->isDirty($field)) { // یعنی تغییر کرده
                    $oldFile = $driver->getOriginal($field);

                    if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }
        });
    }
}
