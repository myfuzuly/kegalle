<?php
$lockFile = __DIR__ . '/seed-listings-once.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use App\Models\Category;
use App\Models\Location;
use App\Models\Listing;
use App\Models\User;

$user = User::first();
if (!$user) { die('No user found.'); }

$categories = Category::where('is_active', 1)->pluck('id', 'slug')->toArray();
$locations = Location::where('is_active', 1)->pluck('id', 'slug')->toArray();

// Fallback IDs
$catDefault = reset($categories) ?: 1;
$locDefault = reset($locations) ?: 1;

function catId($slug, $cats, $default) {
    return $cats[$slug] ?? $default;
}
function locId($slug, $locs, $default) {
    return $locs[$slug] ?? $default;
}

$listings = [
    // Electronics
    ['Samsung 55" Smart TV — 4K UHD', 'electronics', 'kegalle', 185000, 'Used - Like New', 'Samsung 55 inch 4K Smart TV with remote. Crystal clear picture, built-in Netflix and YouTube. Barely used, selling due to upgrade.'],
    ['HP Pavilion Laptop 15.6" — Core i7', 'electronics', 'mawanella', 145000, 'Used - Good', 'HP Pavilion laptop with Intel Core i7, 8GB RAM, 512GB SSD. Great for office work and light gaming. Battery holds 4+ hours.'],
    ['JBL Bluetooth Speaker — Charge 5', 'electronics', 'kegalle', 28000, 'Used - Like New', 'JBL Charge 5 portable Bluetooth speaker. Waterproof, 20-hour battery life. Comes with original charging cable.'],
    ['Canon EOS 200D DSLR Camera Kit', 'electronics', 'rambukkana', 95000, 'Used - Good', 'Canon EOS 200D with 18-55mm lens kit. Perfect for beginners. Includes camera bag, extra battery, and 32GB SD card.'],
    ['Sony PlayStation 5 — Disc Edition', 'electronics', 'kegalle', 165000, 'Used - Like New', 'PS5 Disc Edition with 2 controllers and 3 games (FIFA 24, Spider-Man 2, GTA V). Original box included.'],
    ['Apple AirPods Pro 2nd Gen', 'electronics', 'mawanella', 42000, 'Brand New', 'Brand new sealed Apple AirPods Pro 2nd generation with MagSafe charging case. Warranty card included.'],

    // Mobiles & Tablets
    ['Samsung Galaxy A54 — 128GB', 'mobiles-tablets', 'kegalle', 68000, 'Used - Like New', 'Samsung Galaxy A54 5G, 128GB storage, 8GB RAM. Awesome camera, AMOLED display. Comes with charger and case.'],
    ['iPhone 12 — 64GB Black', 'mobiles-tablets', 'aranayake', 85000, 'Used - Good', 'iPhone 12 64GB in black. Face ID working perfectly, battery health 87%. Screen protector and case included.'],
    ['Xiaomi Redmi Note 13 Pro', 'mobiles-tablets', 'kegalle', 55000, 'Brand New', 'Xiaomi Redmi Note 13 Pro 5G. 256GB storage, 200MP camera. Sealed box with 1 year warranty.'],
    ['iPad Air 4th Gen — 64GB WiFi', 'mobiles-tablets', 'mawanella', 78000, 'Used - Like New', 'iPad Air 4th generation, 64GB, WiFi model. Comes with Apple Pencil 2nd gen and keyboard case.'],
    ['Samsung Galaxy Tab S6 Lite', 'mobiles-tablets', 'rambukkana', 45000, 'Used - Good', 'Samsung Galaxy Tab S6 Lite with S Pen. 64GB, perfect for students. Screen in excellent condition.'],

    // Vehicles
    ['Toyota Vitz 2017 — Auto', 'vehicles', 'kegalle', 6800000, 'Used - Good', 'Toyota Vitz 2017 model, automatic transmission, 1.0L petrol. Full option with push start, reverse camera. 48,000 km.'],
    ['Honda Dio Scooter 2021', 'vehicles', 'mawanella', 420000, 'Used - Like New', 'Honda Dio 110cc scooter, 2021 model. Only 12,000 km. New tyres, full service history. Papers clear.'],
    ['Bajaj Pulsar 150 — 2020', 'vehicles', 'aranayake', 380000, 'Used - Good', 'Bajaj Pulsar 150 dual disc, 2020 model. Well maintained, 28,000 km. New chain and sprocket set.'],
    ['Suzuki WagonR 2018 — Full Option', 'vehicles', 'kegalle', 5950000, 'Used - Good', 'Suzuki WagonR Stingray 2018, automatic, full option. Push start, alloy wheels, reverse camera. 35,000 km mileage.'],

    // Property
    ['3 Bedroom House for Rent — Kegalle Town', 'property', 'kegalle', 35000, 'N/A', '3 bedroom house for rent in Kegalle town, 5 min walk to bus stand. Tiled floors, attached bathroom, parking for 2 vehicles. Available from next month.'],
    ['10 Perch Land for Sale — Mawanella', 'property', 'mawanella', 2800000, 'N/A', '10 perch residential land in Mawanella, flat terrain, road frontage 40ft. Water and electricity available. Clear deed. Ideal for building.'],
    ['Commercial Shop Space — Main Street', 'property', 'kegalle', 55000, 'N/A', 'Ground floor commercial shop space on Kegalle Main Street. 400 sq ft with toilet. High foot traffic area, ideal for retail or office.'],
    ['2 Story House for Sale — Rambukkana', 'property', 'rambukkana', 18500000, 'N/A', 'Newly built 2 story house in Rambukkana. 4 bedrooms, 2 bathrooms, modern kitchen, car porch. 15 perch land. Near school and hospital.'],
    ['1 Bedroom Annex for Rent — Aranayake', 'property', 'aranayake', 15000, 'N/A', 'Separate 1 bedroom annex for rent in Aranayake. Attached bathroom, small kitchen, furnished. Suitable for single person or couple.'],

    // Home & Garden
    ['6-Seater Dining Table — Teak Wood', 'home-garden', 'kegalle', 45000, 'Used - Good', 'Solid teak wood dining table with 6 chairs. Excellent condition, minor surface marks. Heavy and sturdy. Buyer must arrange transport.'],
    ['Samsung Inverter Washing Machine 8kg', 'home-garden', 'mawanella', 52000, 'Used - Like New', 'Samsung front load inverter washing machine, 8kg capacity. Digital display, multiple wash programs. 1.5 years old, under warranty.'],
    ['Garden Tools Set — Complete Kit', 'home-garden', 'kegalle', 8500, 'Brand New', 'Complete garden tools set: spade, rake, pruning shears, watering can, hand trowel, garden gloves. Brand new in carry bag.'],
    ['LG Double Door Refrigerator — 260L', 'home-garden', 'rambukkana', 68000, 'Used - Good', 'LG 260 litre double door fridge with inverter compressor. Frost free, energy efficient. Works perfectly, selling due to upgrade.'],
    ['Ceiling Fan with LED Light — Remote Control', 'home-garden', 'aranayake', 12000, 'Brand New', 'Modern ceiling fan with built-in LED light and remote control. 3 speed settings, 5 blade design. Brand new in box.'],

    // Fashion
    ['Wedding Saree — Designer Heavy Work', 'fashion', 'kegalle', 18000, 'Brand New', 'Designer bridal saree with heavy embroidery work. Maroon and gold color. Comes with matching blouse piece. Worn once for photos only.'],
    ['Men\'s Formal Suit — Size L', 'fashion', 'mawanella', 8500, 'Used - Like New', 'Navy blue formal suit, size L (38-40). Includes jacket, trousers, and matching tie. Worn twice for weddings. Dry cleaned.'],
    ['Nike Running Shoes — Size 42', 'fashion', 'kegalle', 9500, 'Used - Like New', 'Nike Air Zoom Pegasus running shoes, size 42. Very comfortable, excellent grip. Used for about 2 months, in great shape.'],

    // Classified / Services
    ['Tuition Classes — O/L & A/L Mathematics', 'electronics', 'kegalle', 0, 'N/A', 'Experienced mathematics teacher offering tuition classes for O/L and A/L students. Individual and group sessions available. Kegalle town area. WhatsApp for schedule.'],
    ['House Painting Service — Kegalle District', 'home-garden', 'kegalle', 0, 'N/A', 'Professional house painting service covering all areas in Kegalle district. Interior and exterior painting. Free quotation. Quality materials used. 5+ years experience.'],
];

$inserted = 0;
$now = now();

foreach ($listings as $i => $item) {
    [$title, $catSlug, $locSlug, $price, $condition, $description] = $item;

    $slug = \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::random(5);

    $isClassified = ($price === 0);
    $isFeatured = ($i < 10) ? 1 : 0;

    Listing::create([
        'user_id'    => $user->id,
        'category_id'=> catId($catSlug, $categories, $catDefault),
        'location_id'=> locId($locSlug, $locations, $locDefault),
        'ad_type'    => 'free',
        'type'       => $isClassified ? 'classified' : 'product',
        'title'      => $title,
        'slug'       => $slug,
        'description'=> $description,
        'price'      => $price > 0 ? $price : null,
        'currency'   => 'LKR',
        'condition'  => $condition !== 'N/A' ? $condition : null,
        'location'   => ucfirst(str_replace('-', ' ', $locSlug)),
        'status'     => 'approved',
        'is_featured'=> $isFeatured,
        'is_top'     => 0,
        'is_urgent'  => 0,
        'views'      => rand(5, 120),
        'created_at' => $now->copy()->subDays(rand(0, 14))->subHours(rand(0, 23)),
    ]);
    $inserted++;
}

file_put_contents($lockFile, date('Y-m-d H:i:s') . " — Seeded {$inserted} listings");
echo "Done! Inserted {$inserted} listings.";
