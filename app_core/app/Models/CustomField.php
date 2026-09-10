<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['group_id', 'label', 'name', 'type', 'options', 'placeholder', 'unit', 'multi', 'full_width', 'is_required', 'is_searchable', 'sort_order'];

    protected $casts = ['options' => 'array', 'is_required' => 'boolean', 'is_searchable' => 'boolean', 'multi' => 'boolean', 'full_width' => 'boolean'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_custom_field')
            ->withPivot('is_required', 'show_in_filter', 'show_in_list', 'sort_order');
    }
}
