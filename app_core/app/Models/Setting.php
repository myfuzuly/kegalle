<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $all = \Illuminate\Support\Facades\Cache::remember('settings_all', 3600, fn() =>
            static::pluck('value', 'key')->all()
        );
        return $all[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        \Illuminate\Support\Facades\Cache::forget('settings_all');
    }
}
