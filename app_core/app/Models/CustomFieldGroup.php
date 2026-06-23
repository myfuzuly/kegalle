<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldGroup extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];

    public function fields()
    {
        return $this->hasMany(CustomField::class, 'group_id');
    }
}
