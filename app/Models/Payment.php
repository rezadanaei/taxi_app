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
     * Relationship with the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relationship with payable models
     * such as Trip, Wallet, Service, etc.
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Check if the transaction was successful
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if the transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transaction failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
