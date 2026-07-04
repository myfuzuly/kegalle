<?php
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Listing;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// Get first listing
$listing = Listing::with('images')->first();
if (!$listing) { die('No listings found'); }

echo "<h3>Testing save on listing #{$listing->id}: {$listing->title}</h3>";

// Simulate what updateListing does
try {
    $payload = [
        'user_id' => $listing->user_id,
        'store_id' => $listing->store_id,
        'category_id' => $listing->category_id,
        'title' => $listing->title,
        'slug' => $listing->slug,
        'price' => $listing->price ?? 0,
        'type' => $listing->type,
        'status' => $listing->status,
        'location' => $listing->location,
        'description' => $listing->description,
        'is_featured' => $listing->is_featured ?? false,
        'is_top' => $listing->is_top ?? false,
    ];
    if (Schema::hasColumn('listings', 'location_id')) {
        $payload['location_id'] = $listing->location_id;
    }

    $listing->update($payload);
    echo "<p style='color:green'>✅ Listing updated successfully!</p>";
} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Also test: what if category_id is set to a value that doesn't exist?
echo "<h3>Testing with invalid category_id</h3>";
try {
    $listing->update(['category_id' => 99999]);
    echo "<p style='color:orange'>⚠️ No error — category_id 99999 was accepted (no FK constraint)</p>";
    // Revert
    $listing->update(['category_id' => $listing->getOriginal('category_id')]);
} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ FK Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check: does the slug unique validation work properly?
echo "<h3>Slug unique check</h3>";
$otherListing = Listing::where('id', '!=', $listing->id)->first();
if ($otherListing) {
    echo "<p>Other listing slug: {$otherListing->slug}</p>";
    echo "<p>Current listing slug: {$listing->slug}</p>";

    // Simulate validation
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['slug' => $otherListing->slug, 'user_id' => $listing->user_id, 'title' => $listing->title, 'type' => $listing->type, 'status' => $listing->status],
        [
            'slug' => ['nullable', 'string', 'max:220', \Illuminate\Validation\Rule::unique('listings', 'slug')->ignore($listing->id)],
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:180',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
        ]
    );

    if ($validator->fails()) {
        echo "<p style='color:red'>Validation fails as expected: " . htmlspecialchars(json_encode($validator->errors()->all())) . "</p>";
    } else {
        echo "<p style='color:green'>Validation passed (shouldn't happen with duplicate slug)</p>";
    }
}
