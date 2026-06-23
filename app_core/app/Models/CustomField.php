<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['group_id', 'label', 'name', 'type', 'options', 'placeholder', 'is_required', 'is_searchable', 'sort_order'];

    protected $casts = ['options' => 'array', 'is_required' => 'boolean', 'is_searchable' => 'boolean'];
}
