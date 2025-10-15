<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'total_price',
        'discount_percent',
        'net_total',
        'advance_received',
        'remaining_balance',
        'duration_months',
        'monthly_installment',
        'status',
        'sale_date'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'net_total' => 'decimal:2',
        'advance_received' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'duration_months' => 'integer',
        'sale_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}