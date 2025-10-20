<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantor extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'cnic',
        'account_number',
        'address',
        'relationship',
        'profession',
        'father_husband_name',
        'biometric_path'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Generate account number automatically when creating guarantor
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($guarantor) {
            if (empty($guarantor->account_number)) {
                $guarantor->account_number = static::generateAccountNumber();
            }
        });
    }

    /**
     * Generate unique account number for guarantor
     */
    private static function generateAccountNumber()
    {
        do {
            $accountNumber = 'GRT' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        } while (static::where('account_number', $accountNumber)->exists());
        
        return $accountNumber;
    }
}