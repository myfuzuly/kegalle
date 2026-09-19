<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalePrice extends Model
{
    protected $fillable = [
        'commodity', 'category', 'unit',
        'min_price', 'max_price', 'avg_price',
        'market', 'price_date', 'is_active',
    ];

    protected $casts = [
        'price_date' => 'date',
        'min_price'  => 'decimal:2',
        'max_price'  => 'decimal:2',
        'avg_price'  => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public static function latestDate(): ?string
    {
        return static::where('is_active', true)->max('price_date');
    }

    public static function forDate(string $date)
    {
        return static::where('price_date', $date)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('commodity')
            ->get();
    }

    public static function categories(): array
    {
        return [
            'Vegetables', 'Fruits', 'Grains & Pulses',
            'Spices & Condiments', 'Fish & Seafood',
            'Poultry & Eggs', 'Dairy', 'Other',
        ];
    }
}
