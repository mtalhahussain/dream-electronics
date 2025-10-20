<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category_id',
        'name',
        'model',
        'brand',
        'price',
        'purchase_cost',
        'purchased_from',
        'sku',
        'serial_number',
        'stock_quantity',
        'description',
        'purchase_invoice',
        'active'
    ];

    protected $appends = [
        'category_display'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->select('id', 'name', 'color', 'icon', 'slug');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockCredits(): HasMany
    {
        return $this->hasMany(StockCredit::class);
    }

    public function getCategoryDisplayAttribute()
    {
       
        if ($this->category_id && $this->relationLoaded('category') && $this->category && is_object($this->category)) {
            return [
                'type' => 'relationship',
                'name' => $this->category->name,
                'color' => $this->category->color,
                'icon' => $this->category->icon,
                'badge_class' => 'custom'
            ];
        }
       
        return [
            'type' => 'none',
            'name' => 'No Category',
            'color' => '#6c757d',
            'icon' => null,
            'badge_class' => 'bg-secondary'
        ];
    }
}