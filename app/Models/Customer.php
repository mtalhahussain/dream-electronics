<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnic',
        'phone',
        'address',
        'email',
        'biometric_path',
        'face_path'
    ];

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}