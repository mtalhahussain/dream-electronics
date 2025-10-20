<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'cnic',
        'account_number',
        'phone',
        'address',
        'profession',
        'father_husband_name',
        'email',
        'biometric_path',
        'face_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(CustomerDiscount::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Generate account number automatically when creating customer
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            if (empty($customer->account_number)) {
                $customer->account_number = static::generateAccountNumber();
            }
        });
    }

    /**
     * Generate unique account number
     */
    private static function generateAccountNumber()
    {
        do {
            $accountNumber = 'ACC' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        } while (static::where('account_number', $accountNumber)->exists());
        
        return $accountNumber;
    }
}