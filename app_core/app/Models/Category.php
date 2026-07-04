<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'name', 'slug', 'type', 'icon', 'image',
        'description', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function customFields()
    {
        return $this->belongsToMany(CustomField::class, 'category_custom_field')
            ->withPivot('is_required', 'show_in_filter', 'show_in_list', 'sort_order')
            ->orderByPivot('sort_order');
    }
}
