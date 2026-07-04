<?php
/**
 * One-time setup: category-specific listing fields, brands, models, subcategories.
 * DELETE THIS FILE after running.
 */
$lockFile = __DIR__ . '/setup_category_fields.lock';
if (file_exists($lockFile)) { die('Already executed. Delete .lock file to re-run.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

echo "<pre>";

// ── 1. Create brands & brand_models tables ──────────────────────
if (!Schema::hasTable('brands')) {
    Schema::create('brands', function (Blueprint $t) {
        $t->id();
        $t->string('category_group', 50);
        $t->string('name');
        $t->string('slug');
        $t->integer('sort_order')->default(0);
        $t->boolean('is_active')->default(true);
        $t->timestamps();
        $t->index('category_group');
    });
    echo "✓ Created brands table\n";
}

if (!Schema::hasTable('brand_models')) {
    Schema::create('brand_models', function (Blueprint $t) {
        $t->id();
        $t->foreignId('brand_id')->constrained()->cascadeOnDelete();
        $t->string('name');
        $t->string('slug');
        $t->integer('sort_order')->default(0);
        $t->boolean('is_active')->default(true);
        $t->timestamps();
    });
    echo "✓ Created brand_models table\n";
}

// ── 2. Create subcategories ─────────────────────────────────────
$parentMap = [];
$cats = DB::table('categories')->whereNull('parent_id')->get();
foreach ($cats as $c) { $parentMap[strtolower($c->name)] = $c->id; }

$subcategories = [
    'electronics' => [
        'Mobile Phones' => '📱', 'Mobile Accessories' => '🔌', 'Mobile Spare Parts' => '🔧',
        'Smart Products' => '⌚', 'Computers, Laptops & Tablets' => '💻', 'Computer Accessories' => '🖱️',
        'TV' => '📺', 'TV Accessories' => '🔌', 'Camera' => '📷',
        'Audio & Mp3' => '🎧', 'Electronic Home Appliances' => '🏠', 'Video Games & Other Electronics' => '🎮',
        'Aircon & Fittings' => '❄️',
    ],
    'vehicles' => [
        'Cars' => '🚗', 'Bikes' => '🏍️', 'Three Wheelers' => '🛺',
        'Vans' => '🚐', 'Buses' => '🚌', 'Lorries' => '🚛',
        'Heavy Duty' => '🚜', 'Tractor' => '🚜', 'Boats' => '⛵',
        'Bicycle' => '🚲', 'Auto Parts & Accessories' => '🔩',
        'Auto Services & Rentals' => '🔧', 'Maintenance & Repair' => '🛠️',
    ],
    'property' => [
        'Land' => '🏞️', 'Commercial Property' => '🏢', 'House' => '🏠', 'Apartment' => '🏢',
    ],
    'home & garden' => [
        'Furniture' => '🪑', 'Bathrooms' => '🚿', 'Garden' => '🌳',
        'Décor' => '🖼️', 'Kitchen Items' => '🍳', 'Other Items' => '📦',
    ],
];

// Create Animals & Pets parent if not exists
if (!isset($parentMap['animals & pets'])) {
    $pid = DB::table('categories')->insertGetId([
        'parent_id' => null, 'name' => 'Animals & Pets', 'slug' => 'animals-pets',
        'type' => 'both', 'icon' => '🐾', 'sort_order' => 10, 'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $parentMap['animals & pets'] = $pid;
    echo "✓ Created parent category: Animals & Pets\n";
}

$subcategories['animals & pets'] = [
    'Pets' => '🐕', 'Farm Animals' => '🐄', 'Pet Food' => '🦴',
    'Animal Accessories' => '🎾', 'Veterinary Services' => '🏥', 'Other' => '📦',
];

$subCatIds = [];
foreach ($subcategories as $parentName => $subs) {
    $parentId = $parentMap[$parentName] ?? null;
    if (!$parentId) { echo "⚠ Parent '$parentName' not found, skipping\n"; continue; }
    $order = 1;
    foreach ($subs as $name => $icon) {
        $slug = Str::slug($name);
        $existing = DB::table('categories')->where('slug', $slug)->first();
        if ($existing) {
            $subCatIds[$slug] = $existing->id;
            if ($existing->parent_id != $parentId) {
                DB::table('categories')->where('id', $existing->id)->update(['parent_id' => $parentId]);
            }
        } else {
            $id = DB::table('categories')->insertGetId([
                'parent_id' => $parentId, 'name' => $name, 'slug' => $slug,
                'type' => 'both', 'icon' => $icon, 'sort_order' => $order,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $subCatIds[$slug] = $id;
        }
        $order++;
    }
    echo "✓ Created subcategories for: $parentName\n";
}

// ── 3. Create custom field groups ───────────────────────────────
$groups = [
    'general' => 'General', 'mobile' => 'Mobile Specifications',
    'electronics' => 'Electronics', 'vehicle' => 'Vehicle Details',
    'property' => 'Property Details', 'home' => 'Home & Garden', 'animals' => 'Animals & Pets',
];
$groupIds = [];
foreach ($groups as $slug => $name) {
    $existing = DB::table('custom_field_groups')->where('slug', $slug)->first();
    if ($existing) { $groupIds[$slug] = $existing->id; continue; }
    $groupIds[$slug] = DB::table('custom_field_groups')->insertGetId([
        'name' => $name, 'slug' => $slug, 'sort_order' => 0,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}
echo "✓ Created custom field groups\n";

// ── 4. Create custom fields ────────────────────────────────────
$fields = [
    // General
    ['group' => 'general', 'label' => 'Condition', 'name' => 'condition', 'type' => 'select',
     'options' => ['Brand New', 'Used', 'Refurbished', 'Other']],
    ['group' => 'general', 'label' => 'Item Type', 'name' => 'item_type', 'type' => 'select', 'options' => null],
    ['group' => 'general', 'label' => 'Brand', 'name' => 'brand_id', 'type' => 'brand_select', 'options' => null],
    ['group' => 'general', 'label' => 'Model', 'name' => 'model_id', 'type' => 'model_select', 'options' => null],

    // Mobile specific
    ['group' => 'mobile', 'label' => 'Features', 'name' => 'features', 'type' => 'checkbox_group',
     'options' => ['USB Type-B Port', 'USB Type-C Port', 'Fast Charging', 'Flash Charging',
                   'Expandable Memory', 'Bluetooth', 'Wifi', 'GPS', 'Fingerprint Sensor', 'Infrared Port']],
    ['group' => 'mobile', 'label' => 'RAM', 'name' => 'ram', 'type' => 'select',
     'options' => ['1GB', '2GB', '3GB', '4GB', '6GB', '8GB', '12GB', '16GB']],
    ['group' => 'mobile', 'label' => 'Storage', 'name' => 'memory', 'type' => 'select',
     'options' => ['8GB', '16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB']],
    ['group' => 'mobile', 'label' => 'Camera', 'name' => 'camera', 'type' => 'text', 'options' => null,
     'placeholder' => 'e.g. 50MP + 12MP'],
    ['group' => 'mobile', 'label' => 'Screen Size', 'name' => 'screen_size', 'type' => 'text', 'options' => null,
     'placeholder' => 'e.g. 6.7 inches'],
    ['group' => 'mobile', 'label' => 'Battery', 'name' => 'battery', 'type' => 'text', 'options' => null,
     'placeholder' => 'e.g. 5000mAh'],
    ['group' => 'mobile', 'label' => 'Processor', 'name' => 'processor', 'type' => 'text', 'options' => null,
     'placeholder' => 'e.g. Snapdragon 8 Gen 3'],
    ['group' => 'mobile', 'label' => 'Network', 'name' => 'network', 'type' => 'select',
     'options' => ['2G', '3G', '4G LTE', '5G']],
    ['group' => 'mobile', 'label' => 'SIM Support', 'name' => 'sim_support', 'type' => 'select',
     'options' => ['Single SIM', 'Dual SIM', 'Triple SIM', 'eSIM', 'Dual SIM + eSIM']],

    // Electronics - TV
    ['group' => 'electronics', 'label' => 'Screen Type', 'name' => 'screen_type', 'type' => 'select',
     'options' => ['LED', 'OLED', 'QLED', 'LCD', 'Plasma', 'AMOLED', 'Mini LED']],
    ['group' => 'electronics', 'label' => 'Screen Size', 'name' => 'tv_screen_size', 'type' => 'select',
     'options' => ['24"', '32"', '40"', '43"', '50"', '55"', '65"', '75"', '85"']],

    // Vehicle specific
    ['group' => 'vehicle', 'label' => 'Vehicle Type', 'name' => 'vehicle_type', 'type' => 'select',
     'options' => ['Car', 'Van', 'SUV', 'Jeep', 'Pickup', 'Bike', 'Scooter', 'Three Wheeler',
                   'Bus', 'Lorry', 'Tipper', 'Tractor', 'Boat']],
    ['group' => 'vehicle', 'label' => 'Year of Manufacture', 'name' => 'year', 'type' => 'select',
     'options' => array_map('strval', range(date('Y'), 1990, -1))],
    ['group' => 'vehicle', 'label' => 'Mileage (km)', 'name' => 'mileage', 'type' => 'number', 'options' => null,
     'placeholder' => 'e.g. 85000'],
    ['group' => 'vehicle', 'label' => 'Engine Capacity (cc)', 'name' => 'engine_capacity', 'type' => 'text',
     'options' => null, 'placeholder' => 'e.g. 1500cc'],
    ['group' => 'vehicle', 'label' => 'Fuel Type', 'name' => 'fuel_type', 'type' => 'select',
     'options' => ['Petrol', 'Diesel', 'Hybrid', 'Electric', 'CNG', 'LPG']],
    ['group' => 'vehicle', 'label' => 'Transmission', 'name' => 'transmission', 'type' => 'select',
     'options' => ['Manual', 'Automatic', 'Tiptronic', 'CVT']],

    // Property specific
    ['group' => 'property', 'label' => 'Property Type', 'name' => 'property_type', 'type' => 'select',
     'options' => ['For Sale', 'For Rent', 'Lease']],
    ['group' => 'property', 'label' => 'Address', 'name' => 'address', 'type' => 'text', 'options' => null,
     'placeholder' => 'Enter property address'],
    ['group' => 'property', 'label' => 'Size', 'name' => 'size', 'type' => 'text', 'options' => null,
     'placeholder' => 'e.g. 20'],
    ['group' => 'property', 'label' => 'Size Unit', 'name' => 'size_unit', 'type' => 'select',
     'options' => ['Perches', 'Acres', 'Sq ft', 'Sq m']],
    ['group' => 'property', 'label' => 'Ownership Type', 'name' => 'ownership_type', 'type' => 'select',
     'options' => ['Freehold', 'Leasehold', 'Government Lease']],
    ['group' => 'property', 'label' => 'Bedrooms', 'name' => 'bedrooms', 'type' => 'select',
     'options' => ['1', '2', '3', '4', '5', '6', '7', '8+']],
    ['group' => 'property', 'label' => 'Bathrooms', 'name' => 'bathrooms', 'type' => 'select',
     'options' => ['1', '2', '3', '4', '5+']],
    ['group' => 'property', 'label' => 'House Size (sq ft)', 'name' => 'house_size', 'type' => 'text',
     'options' => null, 'placeholder' => 'e.g. 1500'],

    // Home & Garden
    ['group' => 'home', 'label' => 'Type', 'name' => 'home_type', 'type' => 'select', 'options' => null],

    // Animals
    ['group' => 'animals', 'label' => 'Type', 'name' => 'animal_type', 'type' => 'select', 'options' => null],
];

$fieldIds = [];
foreach ($fields as $f) {
    $existing = DB::table('custom_fields')->where('name', $f['name'])->first();
    if ($existing) { $fieldIds[$f['name']] = $existing->id; continue; }
    $fieldIds[$f['name']] = DB::table('custom_fields')->insertGetId([
        'group_id' => $groupIds[$f['group']] ?? null,
        'label' => $f['label'], 'name' => $f['name'], 'type' => $f['type'],
        'options' => $f['options'] ? json_encode($f['options']) : null,
        'placeholder' => $f['placeholder'] ?? null,
        'is_required' => false, 'is_searchable' => true, 'sort_order' => 0,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}
echo "✓ Created custom fields\n";

// ── 5. Link fields to subcategories ─────────────────────────────
$catFieldMap = [
    // Mobile Phones - full spec
    'mobile-phones' => ['condition','brand_id','model_id','features','ram','memory','camera','screen_size','battery','processor','network','sim_support'],
    // Mobile Accessories, Spare Parts, Smart Products
    'mobile-accessories' => ['condition','item_type','brand_id','model_id'],
    'mobile-spare-parts' => ['condition','item_type','brand_id','model_id'],
    'smart-products' => ['condition','item_type','brand_id','model_id'],
    // Computers
    'computers-laptops-tablets' => ['condition','item_type','brand_id','model_id'],
    'computer-accessories' => ['condition','item_type','brand_id','model_id'],
    // TV
    'tv' => ['condition','brand_id','item_type','screen_type','tv_screen_size','model_id'],
    'tv-accessories' => ['condition','brand_id','item_type','model_id'],
    // Camera, Audio, Appliances, Games, Aircon
    'camera' => ['condition','item_type','brand_id','model_id'],
    'audio-mp3' => ['condition','brand_id','item_type','model_id'],
    'electronic-home-appliances' => ['condition','brand_id','item_type','model_id'],
    'video-games-other-electronics' => ['condition','brand_id','item_type','model_id'],
    'aircon-fittings' => ['condition','brand_id','item_type','model_id'],
    // Vehicles
    'cars' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'bikes' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'three-wheelers' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'vans' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'buses' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'lorries' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'heavy-duty' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'tractor' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'boats' => ['vehicle_type','condition','brand_id','model_id','year','mileage','engine_capacity','fuel_type','transmission'],
    'bicycle' => ['vehicle_type','condition','brand_id','model_id'],
    'auto-parts-accessories' => ['item_type'],
    'auto-services-rentals' => ['item_type'],
    'maintenance-repair' => ['item_type'],
    // Property
    'land' => ['property_type','address','size','size_unit','ownership_type'],
    'commercial-property' => ['property_type','address','size','size_unit','ownership_type'],
    'house' => ['property_type','address','bedrooms','bathrooms','size','size_unit','house_size'],
    'apartment' => ['property_type','address','bedrooms','bathrooms','size','size_unit','house_size'],
    // Home & Garden
    'furniture' => ['home_type','condition','brand_id','model_id'],
    'bathrooms' => ['home_type','condition','brand_id','model_id'],
    'garden' => ['home_type','condition','brand_id','model_id'],
    'decor' => ['home_type','condition','brand_id','model_id'],
    'kitchen-items' => ['home_type','condition','brand_id','model_id'],
    'other-items' => ['home_type','condition','brand_id','model_id'],
    // Animals
    'pets' => ['animal_type'],
    'farm-animals' => ['animal_type'],
    'pet-food' => ['animal_type','brand_id'],
    'animal-accessories' => ['animal_type','brand_id'],
    'veterinary-services' => ['animal_type'],
    'other' => ['animal_type'],
];

// Determine brand category_group per subcategory
$brandGroupMap = [
    'mobile-phones' => 'mobile', 'mobile-accessories' => 'mobile', 'mobile-spare-parts' => 'mobile', 'smart-products' => 'mobile',
    'computers-laptops-tablets' => 'computer', 'computer-accessories' => 'computer',
    'tv' => 'tv', 'tv-accessories' => 'tv',
    'camera' => 'camera',
    'audio-mp3' => 'electronics', 'electronic-home-appliances' => 'electronics',
    'video-games-other-electronics' => 'electronics', 'aircon-fittings' => 'electronics',
    'cars' => 'vehicle', 'bikes' => 'vehicle_bike', 'three-wheelers' => 'vehicle',
    'vans' => 'vehicle', 'buses' => 'vehicle', 'lorries' => 'vehicle',
    'heavy-duty' => 'vehicle', 'tractor' => 'vehicle', 'boats' => 'vehicle_boat',
    'bicycle' => 'vehicle_bicycle',
    'furniture' => 'furniture', 'bathrooms' => 'home', 'garden' => 'home',
    'decor' => 'home', 'kitchen-items' => 'home', 'other-items' => 'home',
    'pet-food' => 'pet', 'animal-accessories' => 'pet',
];

DB::table('category_custom_field')->truncate();

foreach ($catFieldMap as $catSlug => $fieldNames) {
    $catId = $subCatIds[$catSlug] ?? DB::table('categories')->where('slug', $catSlug)->value('id');
    if (!$catId) { echo "⚠ Subcategory '$catSlug' not found\n"; continue; }
    $order = 1;
    foreach ($fieldNames as $fn) {
        $fid = $fieldIds[$fn] ?? null;
        if (!$fid) { echo "⚠ Field '$fn' not found\n"; continue; }
        DB::table('category_custom_field')->insert([
            'category_id' => $catId, 'custom_field_id' => $fid,
            'is_required' => in_array($fn, ['condition', 'brand_id']),
            'show_in_filter' => in_array($fn, ['condition', 'brand_id', 'ram', 'memory', 'fuel_type', 'transmission', 'bedrooms']),
            'show_in_list' => true, 'sort_order' => $order++,
        ]);
    }
}
echo "✓ Linked fields to subcategories\n";

// ── 6. Seed brands & models ────────────────────────────────────
$brandData = [
    'mobile' => [
        'Samsung' => ['Galaxy S24 Ultra','Galaxy S24+','Galaxy S24','Galaxy S23','Galaxy A54','Galaxy A34','Galaxy A14','Galaxy M34','Galaxy Z Fold5','Galaxy Z Flip5'],
        'Apple' => ['iPhone 15 Pro Max','iPhone 15 Pro','iPhone 15','iPhone 14','iPhone 13','iPhone SE'],
        'Xiaomi' => ['Redmi Note 13 Pro','Redmi Note 13','Redmi 13C','POCO X6 Pro','POCO M6 Pro','Mi 14'],
        'Huawei' => ['P60 Pro','Nova 11','Y90','Mate 60 Pro'],
        'OPPO' => ['Reno 11','A98','A78','Find X7'],
        'Vivo' => ['V30','Y27','Y17s','X100 Pro'],
        'Realme' => ['12 Pro+','C67','Narzo 60','GT5 Pro'],
        'OnePlus' => ['12','Nord CE 3','Nord N30','11R'],
        'Nokia' => ['G42','C32','G22','XR21'],
        'Sony' => ['Xperia 1 V','Xperia 5 V','Xperia 10 V'],
        'Google' => ['Pixel 8 Pro','Pixel 8','Pixel 7a'],
        'Motorola' => ['Edge 40 Pro','Moto G84','Moto G54'],
        'Nothing' => ['Phone 2','Phone 1'],
        'Tecno' => ['Spark 20 Pro','Camon 20','Pop 7 Pro'],
        'Infinix' => ['Note 30 Pro','Hot 30','Smart 8'],
    ],
    'computer' => [
        'Apple' => ['MacBook Air M2','MacBook Pro 14"','MacBook Pro 16"','iMac','Mac Mini','iPad Pro','iPad Air'],
        'HP' => ['Pavilion','Envy','EliteBook','ProBook','Victus','Spectre'],
        'Dell' => ['Inspiron','XPS','Latitude','Vostro','Alienware'],
        'Lenovo' => ['ThinkPad','IdeaPad','Legion','Yoga','Tab P12'],
        'Asus' => ['ZenBook','VivoBook','ROG Strix','TUF Gaming','ProArt'],
        'Acer' => ['Aspire','Swift','Nitro','Predator'],
        'MSI' => ['GF63','Katana','Raider','Creator'],
        'Samsung' => ['Galaxy Book','Galaxy Tab S9'],
        'Microsoft' => ['Surface Pro','Surface Laptop','Surface Go'],
    ],
    'tv' => [
        'Samsung' => ['Crystal UHD','Neo QLED','OLED','The Frame','The Serif'],
        'LG' => ['OLED C3','OLED B3','NanoCell','UHD','QNED'],
        'Sony' => ['Bravia XR','Bravia X','OLED A80L'],
        'TCL' => ['C Series','P Series','S Series'],
        'Hisense' => ['U8K','A6K','U6K'],
        'Panasonic' => ['OLED','LED','4K Ultra HD'],
        'Philips' => ['OLED','Ambilight','PUS Series'],
        'Abans' => ['LED TV','Smart TV'],
        'Singer' => ['LED TV','Smart TV'],
    ],
    'camera' => [
        'Canon' => ['EOS R5','EOS R6','EOS 90D','PowerShot'],
        'Nikon' => ['Z8','Z6 III','D7500','Coolpix'],
        'Sony' => ['Alpha A7 IV','Alpha A6700','ZV-E10','RX100'],
        'Fujifilm' => ['X-T5','X-S20','X100V'],
        'GoPro' => ['Hero 12','Hero 11','Max'],
        'DJI' => ['Osmo Action 4','Pocket 3'],
    ],
    'electronics' => [
        'Samsung' => [], 'LG' => [], 'Sony' => [], 'Philips' => [],
        'Panasonic' => [], 'JBL' => [], 'Bose' => [], 'Harman Kardon' => [],
        'Marshall' => [], 'Anker' => [], 'Baseus' => [], 'Singer' => [],
        'Abans' => [], 'Midea' => [], 'Haier' => [],
    ],
    'vehicle' => [
        'Toyota' => ['Vitz','Aqua','Prius','Corolla','Axio','Fielder','Premio','Allion','CHR','RAV4','Hilux','KDH','HiAce','Land Cruiser','Rush','Fortuner'],
        'Suzuki' => ['WagonR','Alto','Swift','Celerio','Baleno','Ciaz','Vitara','Jimny','Every','Carry'],
        'Honda' => ['Fit','Vezel','Grace','Civic','City','CRV','HRV','BRV','WRV'],
        'Nissan' => ['March','Note','Leaf','X-Trail','Juke','Caravan','NV200'],
        'Mitsubishi' => ['Lancer','Outlander','Montero','L200','Canter','Rosa'],
        'Hyundai' => ['Tucson','Creta','i20','i10','Accent','Elantra','Staria'],
        'KIA' => ['Sportage','Seltos','Picanto','Sorento','Carnival'],
        'BMW' => ['3 Series','5 Series','X1','X3','X5'],
        'Mercedes-Benz' => ['C-Class','E-Class','GLA','GLC','Sprinter'],
        'Audi' => ['A3','A4','Q3','Q5'],
        'Volkswagen' => ['Polo','Golf','Tiguan'],
        'Ford' => ['Ranger','Everest','EcoSport'],
        'Isuzu' => ['D-Max','Elf','Forward'],
        'Tata' => ['LPT','Ace','Dimo Batta'],
        'Mahindra' => ['Bolero','Scorpio','XUV700','Pik Up'],
        'Perodua' => ['Axia','Myvi','Bezza'],
        'Daihatsu' => ['Mira','Move','Hijet'],
        'MG' => ['ZS','HS','MG5'],
        'Chery' => ['Tiggo 4 Pro','Tiggo 7 Pro','Arrizo 5'],
        'BYD' => ['Atto 3','Dolphin','Seal'],
        'DFSK' => ['Glory 580','Mini Truck'],
        'Bajaj' => ['RE','Pulsar','CT','Discover'],
        'TVS' => ['Apache','Jupiter','Ntorq','XL100'],
        'Yamaha' => ['FZ','R15','MT-15','NMAX','Aerox'],
        'Hero' => ['Splendor','HF Deluxe','Glamour','Xpulse'],
        'Demak' => ['Civic','DTM'],
    ],
    'vehicle_bike' => [
        'Honda' => ['Dio','CB Hornet','Shine','Activa','PCX','CB350','Hornet 2.0'],
        'Bajaj' => ['Pulsar','CT 125','Discover','Dominar','Avenger'],
        'TVS' => ['Apache RTR','Jupiter','Ntorq','Raider','XL100'],
        'Yamaha' => ['FZ','FZS','R15','MT-15','Ray ZR'],
        'Suzuki' => ['Gixxer','Access','Burgman','V-Strom'],
        'Hero' => ['Splendor','HF Deluxe','Glamour','Xpulse','Destini'],
        'Royal Enfield' => ['Classic 350','Meteor 350','Hunter 350','Himalayan'],
        'Demak' => ['Civic','DTM','Tryon'],
    ],
    'vehicle_bicycle' => [
        'Giant' => [], 'Trek' => [], 'Specialized' => [],
        'Scott' => [], 'Merida' => [], 'DSI' => [],
        'Lumala' => [], 'Kenstar' => [],
    ],
    'vehicle_boat' => [
        'Yamaha' => [], 'Honda' => [], 'Suzuki' => [], 'Mercury' => [],
    ],
    'furniture' => [
        'IKEA' => [], 'Damro' => [], 'Arpico' => [], 'Moratuwa Furniture' => [],
        'Singer' => [], 'Other' => [],
    ],
    'home' => [
        'Singer' => [], 'Abans' => [], 'Philips' => [], 'LG' => [],
        'Samsung' => [], 'Midea' => [], 'Other' => [],
    ],
    'pet' => [
        'Royal Canin' => [], 'Pedigree' => [], 'Whiskas' => [],
        'Purina' => [], 'Hills' => [], 'Other' => [],
    ],
];

foreach ($brandData as $group => $brands) {
    foreach ($brands as $brandName => $models) {
        $bSlug = Str::slug($brandName);
        $existing = DB::table('brands')->where('slug', $bSlug)->where('category_group', $group)->first();
        if ($existing) {
            $brandId = $existing->id;
        } else {
            $brandId = DB::table('brands')->insertGetId([
                'category_group' => $group, 'name' => $brandName, 'slug' => $bSlug,
                'sort_order' => 0, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        foreach ($models as $i => $modelName) {
            $mSlug = Str::slug($modelName);
            if (DB::table('brand_models')->where('brand_id', $brandId)->where('slug', $mSlug)->exists()) continue;
            DB::table('brand_models')->insert([
                'brand_id' => $brandId, 'name' => $modelName, 'slug' => $mSlug,
                'sort_order' => $i, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
    echo "✓ Seeded brands for: $group\n";
}

// Store brand group mapping as a JSON config for the JS to use
$catBrandGroupJson = json_encode($brandGroupMap);
// We'll store this in a config-like approach via the category_custom_field

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n✅ Setup complete! Delete this file and the .lock file.\n";
echo "</pre>";
