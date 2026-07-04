<?php
$lockFile = __DIR__ . '/create_explore_subs.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ExploreItem;
use App\Models\ExploreSubItem;

// 1. Add slug, description, content columns to explore_items if missing
if (!Schema::hasColumn('explore_items', 'slug')) {
    Schema::table('explore_items', function (Blueprint $table) {
        $table->string('slug', 190)->nullable()->after('title');
        $table->text('description')->nullable()->after('slug');
        $table->text('content')->nullable()->after('items');
    });
    echo "Added slug, description, content columns.\n";

    // Generate slugs for existing records
    foreach (ExploreItem::all() as $item) {
        $item->slug = Str::slug($item->title);
        $item->save();
    }
    echo "Generated slugs for existing explore items.\n";
}

// 2. Create explore_sub_items table
if (!Schema::hasTable('explore_sub_items')) {
    Schema::create('explore_sub_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('explore_item_id')->constrained()->cascadeOnDelete();
        $table->string('name', 190);
        $table->text('description')->nullable();
        $table->string('phone', 100)->nullable();
        $table->string('email', 190)->nullable();
        $table->string('address', 500)->nullable();
        $table->string('map_url', 500)->nullable();
        $table->string('image')->nullable();
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    echo "Table explore_sub_items created.\n";
}

// Helper
function addSubs($slug, $items) {
    $e = ExploreItem::where('slug', $slug)->first();
    if (!$e) { echo "Explore '$slug' not found.\n"; return; }
    if ($e->subItems()->count() > 0) { echo "Items already exist for '$slug'.\n"; return; }
    foreach ($items as $i => $item) {
        ExploreSubItem::create(array_merge($item, [
            'explore_item_id' => $e->id,
            'sort_order' => $i + 1,
            'is_active' => true,
        ]));
    }
    echo "Seeded " . count($items) . " items for '$slug'.\n";
}

// Update descriptions
ExploreItem::where('slug', 'activities')->update(['description' => 'Hiking, water activities, camping and outdoor adventures across the Kegalle district.']);
ExploreItem::where('slug', 'tourist-places')->update(['description' => 'Popular tourist attractions, heritage sites and must-visit destinations in Kegalle.']);
ExploreItem::where('slug', 'natural-resources')->update(['description' => 'Forests, reservoirs, rivers, waterfalls and nature reserves in the Kegalle district.']);
ExploreItem::where('slug', 'historic-places')->update(['description' => 'Ancient temples, colonial forts, archaeological sites and cultural landmarks.']);

// 3. Seed sub-items

// Activities
addSubs('activities', [
    ['name' => 'Hiking & Trekking', 'description' => 'Trails through rubber and tea estates, forest reserves and viewpoints with Kelani River valley views. Most routes are half-day with a local guide.', 'address' => 'Kegalle, Mawanella & Aranayake hills'],
    ['name' => 'Bopath Ella Falls', 'description' => 'One of Sri Lanka\'s widest waterfalls with a natural pool at the base — popular for swimming after a short walk.', 'address' => 'Kuruwita, Ratnapura border', 'phone' => '045-2265284'],
    ['name' => 'Kelani River Activities', 'description' => 'River swimming, guided riverside walks and nature exploration in the Ruwanwella and Kitulgala-adjacent areas.', 'address' => 'Ruwanwella & Kitulgala area'],
    ['name' => 'Camping & Eco-Stays', 'description' => 'Eco-lodges and homestays in the hills offer camping setups paired with guided nature walks and waterfall visits.', 'address' => 'Various locations, Kegalle District'],
    ['name' => 'Cycling Tours', 'description' => 'Scenic cycling routes through paddy fields, rubber plantations and rural villages. Mountain biking trails in the Aranayake hills.', 'address' => 'Kegalle District'],
    ['name' => 'Bird Watching', 'description' => 'Rich birdlife in the Samanala Nature Reserve and forest patches — over 100 species spotted including endemic Sri Lankan species.', 'address' => 'Samanala Reserve & surroundings'],
]);

// Tourist Places
addSubs('tourist-places', [
    ['name' => 'Pinnawala Elephant Orphanage', 'description' => 'World-famous elephant orphanage home to 80+ elephants. Daily bathing at the Ma Oya river is the highlight.', 'address' => 'Rambukkana Road, Pinnawala', 'phone' => '035-2265284'],
    ['name' => 'Elephant Freedom Wall', 'description' => 'Unique attraction near Pinnawala where elephants roam freely — interactive experiences and close encounters.', 'address' => 'Pinnawala, Rambukkana'],
    ['name' => 'Ruwanwella Suspension Bridge', 'description' => 'Heritage suspension bridge over the Kelani River — scenic views and a popular photo spot.', 'address' => 'Ruwanwella Town'],
    ['name' => 'Aranayake Viewpoint', 'description' => 'Panoramic hilltop viewpoint overlooking the Kegalle valley, tea plantations and surrounding mountains.', 'address' => 'Aranayake, Kegalle District'],
    ['name' => 'Kegalle Town Heritage Walk', 'description' => 'Walking tour through colonial-era buildings, the old clock tower and bustling market streets of Kegalle town.', 'address' => 'Kegalle Town Centre'],
    ['name' => 'Mawanella Spice Gardens', 'description' => 'Guided tours of cinnamon, pepper and clove plantations — learn about traditional spice cultivation.', 'address' => 'Mawanella area'],
    ['name' => 'Kadugannawa Pass', 'description' => 'Historic mountain pass on the Colombo-Kandy road with the Dawson Tower monument and stunning valley views.', 'address' => 'Kadugannawa, Mawanella'],
]);

// Natural Resources
addSubs('natural-resources', [
    ['name' => 'Samanala Nature Reserve', 'description' => 'Protected forest reserve with diverse flora and fauna, trekking trails and bird-watching opportunities.', 'address' => 'Kegalle District'],
    ['name' => 'Kegalle Reservoir', 'description' => 'Scenic reservoir surrounded by hills — popular for evening walks, photography and picnics.', 'address' => 'Kegalle Town outskirts'],
    ['name' => 'Kelani River', 'description' => 'Major river running through the district — supports fishing communities, irrigation and scenic beauty.', 'address' => 'Flows through Ruwanwella, Deraniyagala & Yatiyanthota'],
    ['name' => 'Maha Oya River', 'description' => 'Scenic river famous for the Pinnawala elephant bathing — flows through Rambukkana and surrounding areas.', 'address' => 'Pinnawala & Rambukkana area'],
    ['name' => 'Rubber Plantations', 'description' => 'Kegalle is Sri Lanka\'s rubber heartland — visit working estates to see latex tapping and processing.', 'address' => 'Throughout Kegalle District'],
    ['name' => 'Bathalegala (Bible Rock)', 'description' => 'Distinctive flat-topped mountain resembling an open book. Popular hiking destination with panoramic summit views.', 'address' => 'Aranayake, Kegalle District'],
]);

// Historic Places
addSubs('historic-places', [
    ['name' => 'Old Kegalle Dutch Fort', 'description' => 'Remains of the Dutch colonial fortification in Kegalle town — one of the lesser-known colonial heritage sites.', 'address' => 'Kegalle Town'],
    ['name' => 'Warakapola Archaeological Sites', 'description' => 'Ancient ruins and stone inscriptions dating back centuries, reflecting the region\'s deep historical roots.', 'address' => 'Warakapola area'],
    ['name' => 'Ridi Viharaya (Silver Temple)', 'description' => 'Ancient Buddhist temple dating to the 2nd century BC — stunning murals, moonstone carvings and cave shrines.', 'address' => 'Ridigama, near Kurunegala border', 'phone' => '037-2265100'],
    ['name' => 'Dawson Tower', 'description' => 'Memorial tower at Kadugannawa Pass commemorating Captain Dawson who built the Colombo-Kandy road in 1826.', 'address' => 'Kadugannawa, Mawanella'],
    ['name' => 'Sasseruwa Raja Maha Viharaya', 'description' => 'Ancient rock temple with a massive unfinished Buddha statue carved into the cliff face.', 'address' => 'Near Kegalle District border'],
    ['name' => 'Mawanella Jumma Mosque', 'description' => 'Historic mosque in Mawanella town — one of the oldest mosques in the Sabaragamuwa province.', 'address' => 'Mawanella Town'],
]);

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\nAll done!";
