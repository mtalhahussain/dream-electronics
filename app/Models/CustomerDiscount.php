<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'discount_type',
        'discount_value',
        'category',
        'valid_from',
        'valid_until',
        'is_active',
        'description'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
