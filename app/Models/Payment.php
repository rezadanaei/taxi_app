<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payable_id',
        'payable_type',
        'amount',
        'authority',
        'ref_id',
        'status',
        'type',
    ];

    /**
     * ارتباط با کاربر
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ارتباط polymorphic با مدل‌های پرداخت‌شده
     * مانند Trip، Wallet، Service و غیره
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * بررسی موفقیت تراکنش
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * بررسی در حال پرداخت بودن تراکنش
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * بررسی تراکنش ناموفق
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
