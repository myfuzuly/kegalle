<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['category_id', 'category_group', 'name', 'slug', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'brand_category');
    }

    public function models()
    {
        return $this->hasMany(BrandModel::class)->orderBy('sort_order')->orderBy('name');
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('name');
    }
}
