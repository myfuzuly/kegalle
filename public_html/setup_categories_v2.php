<?php
/**
 * V2: Full category restructure — 22 parent categories with subcategories.
 * Updates conditions to expanded list. Links custom fields to new subcategories.
 * DELETE THIS FILE after running.
 */
$lockFile = __DIR__ . '/setup_categories_v2.lock';
if (file_exists($lockFile)) { die('Already executed. Delete .lock to re-run.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "<pre>";

// ── 1. Update condition field options ───────────────────────────
DB::table('custom_fields')->where('name', 'condition')->update([
    'options' => json_encode(['Brand New','Open Box','Like New','Excellent','Good','Fair','Used','Refurbished','Handmade','Vintage','Damaged','For Parts']),
]);
echo "✓ Updated condition options\n";

// ── 2. Define full category tree ────────────────────────────────
$tree = [
    'Vehicles' => ['icon' => '🚗', 'subs' => [
        'Cars','SUVs & Jeeps','Vans','Pickups','Three Wheelers','Motorcycles','Bicycles','Buses',
        'Trucks & Lorries','Tractors','Heavy Machinery','Electric Vehicles','Boats & Watercraft',
        'Spare Parts','Tyres & Wheels','Accessories','Vehicle Services','Rentals','Insurance',
    ]],
    'Property' => ['icon' => '🏠', 'subs' => [
        'Houses for Sale','Land for Sale','Apartments','Commercial Property','Houses for Rent',
        'Rooms & Annexes','Holiday Rentals','New Projects','Property Services',
    ]],
    'Mobile Phones & Tablets' => ['icon' => '📱', 'subs' => [
        'Smartphones','Feature Phones','Tablets','Smart Watches','Accessories','Repairs & Parts','SIM & Telecom',
    ]],
    'Electronics' => ['icon' => '💻', 'subs' => [
        'Computers','Laptops','Networking','TV & Audio','Gaming','Cameras','Smart Home','Office Electronics','Electronic Components',
    ]],
    'Home Appliances' => ['icon' => '🔌', 'subs' => [
        'Kitchen Appliances','Large Appliances','Cleaning Appliances','Cooling & Heating','Small Appliances',
    ]],
    'Home, Furniture & Garden' => ['icon' => '🪑', 'subs' => [
        'Furniture','Kitchen & Dining','Bathroom','Lighting','Decor','Garden','Plants','Home Improvement',
    ]],
    'Fashion & Beauty' => ['icon' => '👗', 'subs' => [
        'Men','Women','Kids','Shoes','Bags','Jewellery','Beauty','Perfumes','Salon Equipment',
    ]],
    'Babies & Kids' => ['icon' => '👶', 'subs' => [
        'Baby Gear','Baby Clothing','Toys','School Supplies','Strollers','Car Seats',
    ]],
    'Sports, Fitness & Outdoor' => ['icon' => '⚽', 'subs' => [
        'Gym Equipment','Sports Equipment','Camping','Cycling','Fishing','Fitness Supplements',
    ]],
    'Books, Music & Hobbies' => ['icon' => '📚', 'subs' => [
        'Books','Musical Instruments','Art Supplies','Collectibles','Craft Supplies',
    ]],
    'Animals & Pets' => ['icon' => '🐾', 'subs' => [
        'Dogs','Cats','Birds','Fish','Farm Animals','Pet Food','Accessories','Veterinary','Adoption',
    ]],
    'Agriculture' => ['icon' => '🌾', 'subs' => [
        'Farm Machinery','Seeds','Plants','Fertilizer','Livestock','Animal Feed','Irrigation','Agricultural Services',
    ]],
    'Business & Industrial' => ['icon' => '🏭', 'subs' => [
        'Machinery','Manufacturing','Wholesale','Construction Materials','Office Equipment','Solar','Generators','Business Opportunities',
    ]],
    'Jobs' => ['icon' => '💼', 'subs' => [
        'Full Time','Part Time','Contract','Freelance','Internships','Remote','Government','Private Sector',
    ]],
    'Overseas Jobs' => ['icon' => '✈️', 'subs' => [
        'Middle East','Europe','Asia','Australia','Canada','USA','Visa Services','Recruitment Agencies',
    ]],
    'Services' => ['icon' => '🔧', 'subs' => [
        'Home','Construction','Electrical','IT','Marketing','Photography','Transport','Legal','Accounting','Healthcare','Education','Travel',
    ]],
    'Food & Essentials' => ['icon' => '🍎', 'subs' => [
        'Groceries','Fresh Produce','Meat','Seafood','Bakery','Beverages','Household Supplies','Health Products',
    ]],
    'Education' => ['icon' => '🎓', 'subs' => [
        'Schools','Tuition','Courses','Training','Books','Stationery','Online Learning',
    ]],
    'Community' => ['icon' => '🤝', 'subs' => [
        'Events','Lost & Found','Donations','Volunteer','Announcements',
    ]],
    'Tourism & Kegalle Explore' => ['icon' => '🏞️', 'subs' => [
        'Hotels','Homestays','Restaurants','Tourist Attractions','Nature','Historical Places',
    ]],
    'Classifieds' => ['icon' => '📋', 'subs' => [
        'Wanted','Give Away','Swap','Auctions','Clearance','Free Items','Miscellaneous',
    ]],
];

// ── 3. Deactivate old categories that don't match new structure ─
// First collect new parent names (lowered) for matching
$newParentNames = array_map('strtolower', array_keys($tree));

// ── 4. Create/update parent categories ──────────────────────────
$parentIds = [];
$order = 1;
foreach ($tree as $name => $data) {
    $slug = Str::slug($name);
    $existing = DB::table('categories')->where('slug', $slug)->whereNull('parent_id')->first();
    if ($existing) {
        DB::table('categories')->where('id', $existing->id)->update([
            'name' => $name, 'icon' => $data['icon'], 'sort_order' => $order, 'is_active' => true, 'parent_id' => null,
        ]);
        $parentIds[$name] = $existing->id;
    } else {
        // Try matching by similar name
        $similar = DB::table('categories')->whereNull('parent_id')
            ->where(function($q) use ($name) {
                $q->where('name', 'like', '%'.explode(' ', $name)[0].'%');
            })->first();
        if ($similar && !isset($parentIds[$similar->name])) {
            DB::table('categories')->where('id', $similar->id)->update([
                'name' => $name, 'slug' => $slug, 'icon' => $data['icon'], 'sort_order' => $order, 'is_active' => true,
            ]);
            $parentIds[$name] = $similar->id;
        } else {
            $parentIds[$name] = DB::table('categories')->insertGetId([
                'parent_id' => null, 'name' => $name, 'slug' => $slug,
                'type' => 'both', 'icon' => $data['icon'], 'sort_order' => $order,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
    $order++;
}
echo "✓ Created/updated " . count($parentIds) . " parent categories\n";

// ── 5. Create subcategories ─────────────────────────────────────
$subIds = [];
foreach ($tree as $parentName => $data) {
    $pid = $parentIds[$parentName];
    $subOrder = 1;
    foreach ($data['subs'] as $subName) {
        $slug = Str::slug($subName);
        // Handle duplicate slugs across parents by prefixing
        $uniqueSlug = $slug;
        $existingOther = DB::table('categories')->where('slug', $slug)->where('parent_id', '!=', $pid)->first();
        if ($existingOther) {
            $uniqueSlug = Str::slug($parentName) . '-' . $slug;
        }

        $existing = DB::table('categories')
            ->where('parent_id', $pid)
            ->where(function($q) use ($slug, $uniqueSlug, $subName) {
                $q->where('slug', $slug)->orWhere('slug', $uniqueSlug)->orWhere('name', $subName);
            })->first();

        if ($existing) {
            DB::table('categories')->where('id', $existing->id)->update([
                'name' => $subName, 'sort_order' => $subOrder, 'is_active' => true,
            ]);
            $subIds[$pid . ':' . $subName] = $existing->id;
        } else {
            // Check if slug already taken
            $finalSlug = $uniqueSlug;
            if (DB::table('categories')->where('slug', $finalSlug)->exists()) {
                $finalSlug = Str::slug($parentName) . '-' . $slug;
                if (DB::table('categories')->where('slug', $finalSlug)->exists()) {
                    $finalSlug = $finalSlug . '-' . uniqid();
                }
            }
            $subIds[$pid . ':' . $subName] = DB::table('categories')->insertGetId([
                'parent_id' => $pid, 'name' => $subName, 'slug' => $finalSlug,
                'type' => 'both', 'icon' => null, 'sort_order' => $subOrder,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $subOrder++;
    }
}
echo "✓ Created/updated subcategories\n";

// ── 6. Deactivate orphan old categories ─────────────────────────
// Old parent cats that don't match any new parent
$allNewParentIds = array_values($parentIds);
$oldParents = DB::table('categories')->whereNull('parent_id')->whereNotIn('id', $allNewParentIds)->get();
foreach ($oldParents as $op) {
    // Move their subcategories to the closest matching new parent or deactivate
    DB::table('categories')->where('id', $op->id)->update(['is_active' => false]);
    DB::table('categories')->where('parent_id', $op->id)->update(['is_active' => false]);
    echo "  Deactivated old parent: {$op->name} (id={$op->id})\n";
}

// ── 7. Link custom fields to new subcategories ──────────────────
// Get field IDs
$fieldMap = [];
$allFields = DB::table('custom_fields')->get();
foreach ($allFields as $f) { $fieldMap[$f->name] = $f->id; }

// Helper: get subcategory ID by parent name + sub name
function getSubId($parentName, $subName) {
    return DB::table('categories')
        ->where('name', $subName)
        ->whereIn('parent_id', function($q) use ($parentName) {
            $q->select('id')->from('categories')->where('name', $parentName)->whereNull('parent_id');
        })
        ->value('id');
}

// Helper: link fields to a category
function linkFields($catId, $fieldNames, &$fieldMap) {
    if (!$catId) return;
    $order = 1;
    foreach ($fieldNames as $fn) {
        $fid = $fieldMap[$fn] ?? null;
        if (!$fid) continue;
        $exists = DB::table('category_custom_field')->where('category_id', $catId)->where('custom_field_id', $fid)->exists();
        if (!$exists) {
            DB::table('category_custom_field')->insert([
                'category_id' => $catId, 'custom_field_id' => $fid,
                'is_required' => in_array($fn, ['condition', 'brand_id']),
                'show_in_filter' => in_array($fn, ['condition', 'brand_id', 'ram', 'memory', 'fuel_type', 'transmission', 'bedrooms']),
                'show_in_list' => true, 'sort_order' => $order,
            ]);
        }
        $order++;
    }
}

// Add brand_group mapping for new subcategories
$newBrandGroups = [
    'Smartphones' => 'mobile', 'Feature Phones' => 'mobile', 'Tablets' => 'mobile',
    'Smart Watches' => 'mobile', 'Computers' => 'computer', 'Laptops' => 'computer',
    'TV & Audio' => 'tv', 'Cameras' => 'camera', 'Gaming' => 'electronics',
    'Smart Home' => 'electronics', 'Office Electronics' => 'electronics',
    'Kitchen Appliances' => 'home', 'Large Appliances' => 'home',
    'Cleaning Appliances' => 'home', 'Cooling & Heating' => 'home', 'Small Appliances' => 'home',
];

// Vehicles - full spec fields
$vehicleFullSubs = ['Cars','SUVs & Jeeps','Vans','Pickups','Three Wheelers','Motorcycles','Buses','Trucks & Lorries','Tractors','Heavy Machinery','Electric Vehicles','Boats & Watercraft'];
$vehicleFields = ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'];
foreach ($vehicleFullSubs as $sub) {
    $catId = getSubId('Vehicles', $sub);
    linkFields($catId, $vehicleFields, $fieldMap);
}

// Vehicles - simple
foreach (['Bicycles'] as $sub) {
    linkFields(getSubId('Vehicles', $sub), ['vehicle_type','condition','brand_id','model_id'], $fieldMap);
}
foreach (['Spare Parts','Tyres & Wheels','Accessories','Vehicle Services','Rentals','Insurance'] as $sub) {
    linkFields(getSubId('Vehicles', $sub), ['item_type','condition'], $fieldMap);
}

// Mobile - full spec
$mobileFields = ['condition','brand_id','model_id','features','ram','memory','camera','screen_size','battery','processor','network','sim_support'];
linkFields(getSubId('Mobile Phones & Tablets', 'Smartphones'), $mobileFields, $fieldMap);
linkFields(getSubId('Mobile Phones & Tablets', 'Feature Phones'), ['condition','brand_id','model_id','network','sim_support'], $fieldMap);
linkFields(getSubId('Mobile Phones & Tablets', 'Tablets'), ['condition','brand_id','model_id','ram','memory','screen_size','battery','processor'], $fieldMap);
linkFields(getSubId('Mobile Phones & Tablets', 'Smart Watches'), ['condition','brand_id','model_id'], $fieldMap);
foreach (['Accessories','Repairs & Parts','SIM & Telecom'] as $sub) {
    linkFields(getSubId('Mobile Phones & Tablets', $sub), ['condition','item_type','brand_id'], $fieldMap);
}

// Electronics
linkFields(getSubId('Electronics', 'Computers'), ['condition','brand_id','model_id','ram','memory','processor'], $fieldMap);
linkFields(getSubId('Electronics', 'Laptops'), ['condition','brand_id','model_id','ram','memory','screen_size','processor'], $fieldMap);
linkFields(getSubId('Electronics', 'TV & Audio'), ['condition','brand_id','model_id','screen_type','tv_screen_size'], $fieldMap);
linkFields(getSubId('Electronics', 'Cameras'), ['condition','brand_id','model_id'], $fieldMap);
foreach (['Networking','Gaming','Smart Home','Office Electronics','Electronic Components'] as $sub) {
    linkFields(getSubId('Electronics', $sub), ['condition','item_type','brand_id','model_id'], $fieldMap);
}

// Property
$propSaleSubs = ['Houses for Sale','Land for Sale','Commercial Property','New Projects'];
foreach ($propSaleSubs as $sub) {
    linkFields(getSubId('Property', $sub), ['property_type','address','size','size_unit','ownership_type'], $fieldMap);
}
foreach (['Houses for Rent','Rooms & Annexes','Holiday Rentals'] as $sub) {
    linkFields(getSubId('Property', $sub), ['property_type','address','bedrooms','bathrooms','size','size_unit'], $fieldMap);
}
linkFields(getSubId('Property', 'Apartments'), ['property_type','address','bedrooms','bathrooms','size','size_unit','house_size'], $fieldMap);
linkFields(getSubId('Property', 'Property Services'), ['item_type'], $fieldMap);

// Home Appliances
foreach (['Kitchen Appliances','Large Appliances','Cleaning Appliances','Cooling & Heating','Small Appliances'] as $sub) {
    linkFields(getSubId('Home Appliances', $sub), ['condition','brand_id','model_id'], $fieldMap);
}

// Home, Furniture & Garden
foreach (['Furniture','Kitchen & Dining','Bathroom','Lighting','Decor','Garden','Plants','Home Improvement'] as $sub) {
    linkFields(getSubId('Home, Furniture & Garden', $sub), ['condition','brand_id'], $fieldMap);
}

// Fashion & Beauty
foreach (['Men','Women','Kids','Shoes','Bags','Jewellery','Beauty','Perfumes','Salon Equipment'] as $sub) {
    linkFields(getSubId('Fashion & Beauty', $sub), ['condition','brand_id'], $fieldMap);
}

// Animals & Pets
foreach (['Dogs','Cats','Birds','Fish','Farm Animals','Adoption'] as $sub) {
    linkFields(getSubId('Animals & Pets', $sub), ['animal_type'], $fieldMap);
}
foreach (['Pet Food','Accessories'] as $sub) {
    linkFields(getSubId('Animals & Pets', $sub), ['animal_type','brand_id'], $fieldMap);
}
linkFields(getSubId('Animals & Pets', 'Veterinary'), ['animal_type'], $fieldMap);

// Simple categories - just condition
$simpleConditionCats = [
    'Babies & Kids' => ['Baby Gear','Baby Clothing','Toys','School Supplies','Strollers','Car Seats'],
    'Sports, Fitness & Outdoor' => ['Gym Equipment','Sports Equipment','Camping','Cycling','Fishing','Fitness Supplements'],
    'Books, Music & Hobbies' => ['Books','Musical Instruments','Art Supplies','Collectibles','Craft Supplies'],
    'Agriculture' => ['Farm Machinery','Seeds','Plants','Fertilizer','Livestock','Animal Feed','Irrigation','Agricultural Services'],
    'Business & Industrial' => ['Machinery','Manufacturing','Wholesale','Construction Materials','Office Equipment','Solar','Generators','Business Opportunities'],
];
foreach ($simpleConditionCats as $parent => $subs) {
    foreach ($subs as $sub) {
        linkFields(getSubId($parent, $sub), ['condition'], $fieldMap);
    }
}

// No-field categories (just type selection, no extra attributes needed):
// Jobs, Overseas Jobs, Services, Food, Education, Community, Tourism, Classifieds
// These don't need custom fields - the subcategory itself is the classification

echo "✓ Linked custom fields to subcategories\n";

// ── 8. Add new brand groups for new subcategories ───────────────
// Add vehicle brands for new sub-types
$newVehicleBrands = [
    'vehicle_suv' => ['Toyota','Mitsubishi','Nissan','Hyundai','KIA','Mahindra','Suzuki','Honda','Ford','MG','Chery','BYD'],
    'vehicle_ev' => ['Tesla','BYD','MG','Nissan','Hyundai','KIA','BMW','Mercedes-Benz','Chery'],
];
foreach ($newVehicleBrands as $group => $brands) {
    foreach ($brands as $brandName) {
        $bSlug = Str::slug($brandName);
        if (DB::table('brands')->where('slug', $bSlug)->where('category_group', $group)->exists()) continue;
        DB::table('brands')->insert([
            'category_group' => $group, 'name' => $brandName, 'slug' => $bSlug,
            'sort_order' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
echo "✓ Added new brand groups\n";

// ── 9. Update brand group map for new mobile subcategory ────────
// Smartphones uses 'mobile' group (same as old Mobile Phones)
// This is handled by the API route's brandGroupMap — we need to update that too
// For now the JS will use the slug-based mapping

// ── Summary ─────────────────────────────────────────────────────
$totalParents = DB::table('categories')->whereNull('parent_id')->where('is_active', true)->count();
$totalSubs = DB::table('categories')->whereNotNull('parent_id')->where('is_active', true)->count();
$totalLinks = DB::table('category_custom_field')->count();
echo "\n📊 Summary:\n";
echo "  Parent categories: $totalParents\n";
echo "  Subcategories: $totalSubs\n";
echo "  Field-category links: $totalLinks\n";

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n✅ Category restructure complete! Delete this file and the .lock file.\n";
echo "</pre>";
