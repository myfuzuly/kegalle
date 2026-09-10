<?php

namespace Tests;

use App\Models\Location;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** Create a Location row so location_id FK constraints pass in tests. */
    protected function createLocation(array $attrs = []): Location
    {
        $name = $attrs['name'] ?? 'Kegalle';
        return Location::firstOrCreate(
            ['slug' => $attrs['slug'] ?? Str::slug($name)],
            array_merge(['name' => $name, 'is_active' => true, 'sort_order' => 1], $attrs)
        );
    }
}
