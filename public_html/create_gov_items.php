<?php
$lockFile = __DIR__ . '/create_gov_items.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\GovernmentService;
use App\Models\GovernmentServiceItem;

// Create table
if (!Schema::hasTable('government_service_items')) {
    Schema::create('government_service_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('government_service_id')->constrained()->cascadeOnDelete();
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
    echo "Table created.\n";
}

// Helper
function svc($slug) { return GovernmentService::where('slug', $slug)->first(); }
function addItems($slug, $items) {
    $s = svc($slug);
    if (!$s) { echo "Service '$slug' not found.\n"; return; }
    if ($s->items()->count() > 0) { echo "Items already exist for '$slug'.\n"; return; }
    foreach ($items as $i => $item) {
        GovernmentServiceItem::create(array_merge($item, [
            'government_service_id' => $s->id,
            'sort_order' => $i + 1,
            'is_active' => true,
        ]));
    }
    echo "Seeded " . count($items) . " items for '$slug'.\n";
}

// 1. DS Offices (11)
addItems('ds-office', [
    ['name' => 'Aranayake Divisional Secretariat', 'address' => 'Aranayake, Kegalle District', 'phone' => '035-2257002'],
    ['name' => 'Bulathkohupitiya Divisional Secretariat', 'address' => 'Bulathkohupitiya, Kegalle District', 'phone' => '036-2260275'],
    ['name' => 'Dehiovita Divisional Secretariat', 'address' => 'Dehiovita, Kegalle District', 'phone' => '036-2267275'],
    ['name' => 'Deraniyagala Divisional Secretariat', 'address' => 'Deraniyagala, Kegalle District', 'phone' => '036-2269275'],
    ['name' => 'Galigamuwa Divisional Secretariat', 'address' => 'Galigamuwa, Kegalle District', 'phone' => '035-2265275'],
    ['name' => 'Kegalle Divisional Secretariat', 'address' => 'Kegalle Town, Kegalle District', 'phone' => '035-2222275'],
    ['name' => 'Mawanella Divisional Secretariat', 'address' => 'Mawanella, Kegalle District', 'phone' => '035-2246275'],
    ['name' => 'Rambukkana Divisional Secretariat', 'address' => 'Rambukkana, Kegalle District', 'phone' => '035-2264275'],
    ['name' => 'Ruwanwella Divisional Secretariat', 'address' => 'Ruwanwella, Kegalle District', 'phone' => '036-2266275'],
    ['name' => 'Warakapola Divisional Secretariat', 'address' => 'Warakapola, Kegalle District', 'phone' => '035-2267275'],
    ['name' => 'Yatiyanthota Divisional Secretariat', 'address' => 'Yatiyanthota, Kegalle District', 'phone' => '036-2250275'],
]);

// 2. Municipal Council / Pradeshiya Sabha
addItems('municipal-council', [
    ['name' => 'Kegalle Urban Council', 'address' => 'Main Street, Kegalle', 'phone' => '035-2222275', 'description' => 'Urban council governing Kegalle town area.'],
    ['name' => 'Mawanella Urban Council', 'address' => 'Mawanella Town', 'phone' => '035-2246200', 'description' => 'Urban council governing Mawanella town area.'],
    ['name' => 'Ruwanwella Pradeshiya Sabha', 'address' => 'Ruwanwella', 'phone' => '036-2266200', 'description' => 'Local government body for Ruwanwella area.'],
    ['name' => 'Warakapola Pradeshiya Sabha', 'address' => 'Warakapola', 'phone' => '035-2267200', 'description' => 'Local government body for Warakapola area.'],
    ['name' => 'Rambukkana Pradeshiya Sabha', 'address' => 'Rambukkana', 'phone' => '035-2264200', 'description' => 'Local government body for Rambukkana area.'],
    ['name' => 'Galigamuwa Pradeshiya Sabha', 'address' => 'Galigamuwa', 'phone' => '035-2265200', 'description' => 'Local government body for Galigamuwa area.'],
    ['name' => 'Aranayake Pradeshiya Sabha', 'address' => 'Aranayake', 'phone' => '035-2257200', 'description' => 'Local government body for Aranayake area.'],
    ['name' => 'Deraniyagala Pradeshiya Sabha', 'address' => 'Deraniyagala', 'phone' => '036-2269200', 'description' => 'Local government body for Deraniyagala area.'],
    ['name' => 'Dehiovita Pradeshiya Sabha', 'address' => 'Dehiovita', 'phone' => '036-2267200', 'description' => 'Local government body for Dehiovita area.'],
    ['name' => 'Bulathkohupitiya Pradeshiya Sabha', 'address' => 'Bulathkohupitiya', 'phone' => '036-2260200', 'description' => 'Local government body for Bulathkohupitiya area.'],
    ['name' => 'Yatiyanthota Pradeshiya Sabha', 'address' => 'Yatiyanthota', 'phone' => '036-2250200', 'description' => 'Local government body for Yatiyanthota area.'],
]);

// 3. Police Stations
addItems('police', [
    ['name' => 'Kegalle Police Station', 'address' => 'Main Street, Kegalle', 'phone' => '035-2222222', 'description' => 'District headquarters police station.'],
    ['name' => 'Mawanella Police Station', 'address' => 'Mawanella Town', 'phone' => '035-2246222'],
    ['name' => 'Ruwanwella Police Station', 'address' => 'Ruwanwella Town', 'phone' => '036-2266222'],
    ['name' => 'Warakapola Police Station', 'address' => 'Warakapola Town', 'phone' => '035-2267222'],
    ['name' => 'Rambukkana Police Station', 'address' => 'Rambukkana Town', 'phone' => '035-2264222'],
    ['name' => 'Galigamuwa Police Station', 'address' => 'Galigamuwa Town', 'phone' => '035-2265222'],
    ['name' => 'Aranayake Police Station', 'address' => 'Aranayake Town', 'phone' => '035-2257222'],
    ['name' => 'Deraniyagala Police Station', 'address' => 'Deraniyagala Town', 'phone' => '036-2269222'],
    ['name' => 'Yatiyanthota Police Station', 'address' => 'Yatiyanthota Town', 'phone' => '036-2250222'],
    ['name' => 'Dehiovita Police Station', 'address' => 'Dehiovita Town', 'phone' => '036-2267230'],
    ['name' => 'Bulathkohupitiya Police Station', 'address' => 'Bulathkohupitiya Town', 'phone' => '036-2260222'],
]);

// 4. Hospitals
addItems('hospitals', [
    ['name' => 'District General Hospital Kegalle', 'address' => 'Hospital Road, Kegalle', 'phone' => '035-2222261', 'description' => 'Main government hospital in Kegalle district with emergency, OPD, surgical and maternity services.'],
    ['name' => 'Base Hospital Warakapola', 'address' => 'Warakapola Town', 'phone' => '035-2267261', 'description' => 'Base hospital serving Warakapola and surrounding areas.'],
    ['name' => 'Base Hospital Mawanella', 'address' => 'Mawanella Town', 'phone' => '035-2246261', 'description' => 'Base hospital serving Mawanella area.'],
    ['name' => 'Divisional Hospital Ruwanwella', 'address' => 'Ruwanwella Town', 'phone' => '036-2266261', 'description' => 'Divisional hospital with general medical facilities.'],
    ['name' => 'Divisional Hospital Rambukkana', 'address' => 'Rambukkana Town', 'phone' => '035-2264261', 'description' => 'Divisional hospital serving Rambukkana area.'],
    ['name' => 'Divisional Hospital Deraniyagala', 'address' => 'Deraniyagala Town', 'phone' => '036-2269261', 'description' => 'Divisional hospital serving Deraniyagala area.'],
    ['name' => 'Divisional Hospital Galigamuwa', 'address' => 'Galigamuwa Town', 'phone' => '035-2265261', 'description' => 'Divisional hospital serving Galigamuwa area.'],
    ['name' => 'Divisional Hospital Aranayake', 'address' => 'Aranayake Town', 'phone' => '035-2257261', 'description' => 'Divisional hospital serving Aranayake area.'],
    ['name' => 'Divisional Hospital Yatiyanthota', 'address' => 'Yatiyanthota Town', 'phone' => '036-2250261', 'description' => 'Divisional hospital serving Yatiyanthota area.'],
]);

// 5. Schools
addItems('schools', [
    ['name' => 'Kegalle Maha Vidyalaya', 'address' => 'Kegalle Town', 'phone' => '035-2222350', 'description' => 'National school in Kegalle town.'],
    ['name' => 'Mawanella Central College', 'address' => 'Mawanella', 'phone' => '035-2246350', 'description' => 'Central college in Mawanella.'],
    ['name' => 'Ruwanwella Central College', 'address' => 'Ruwanwella', 'phone' => '036-2266350', 'description' => 'Central college in Ruwanwella.'],
    ['name' => 'Warakapola Central College', 'address' => 'Warakapola', 'phone' => '035-2267350', 'description' => 'Central college in Warakapola.'],
    ['name' => 'Rambukkana Central College', 'address' => 'Rambukkana', 'phone' => '035-2264350', 'description' => 'Central college in Rambukkana.'],
    ['name' => 'Zonal Education Office Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222400', 'description' => 'Zonal education office managing schools in Kegalle zone.'],
    ['name' => 'Zonal Education Office Mawanella', 'address' => 'Mawanella', 'phone' => '035-2246400', 'description' => 'Zonal education office managing schools in Mawanella zone.'],
    ['name' => 'Zonal Education Office Dehiovita', 'address' => 'Dehiovita', 'phone' => '036-2267400', 'description' => 'Zonal education office managing schools in Dehiovita zone.'],
]);

// 6. Public Services
addItems('public-services', [
    ['name' => 'District Secretariat Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222235', 'description' => 'District-level administrative office overseeing all divisional secretariats.'],
    ['name' => 'Samurdhi Authority — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222500', 'description' => 'Samurdhi welfare programme — poverty alleviation, livelihood support and monthly allowances.'],
    ['name' => 'Department of Pensions — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222600', 'description' => 'Pension services for government retirees in the district.'],
    ['name' => 'Motor Traffic Department — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222700', 'description' => 'Vehicle registration, driving licences and revenue licences.'],
    ['name' => 'Labour Department — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222450', 'description' => 'Labour rights, EPF/ETF, industrial disputes and worker welfare.'],
    ['name' => 'Agrarian Services Centre', 'address' => 'Kegalle Town', 'phone' => '035-2222550', 'description' => 'Agricultural support, farmer registration and crop insurance.'],
    ['name' => 'Postal Department — Kegalle', 'address' => 'Main Street, Kegalle', 'phone' => '035-2222223', 'description' => 'Main post office — postal services, registered mail and money orders.'],
]);

// 7. Waste Collection
addItems('waste-collection', [
    ['name' => 'Kegalle Urban Council — Waste Collection', 'address' => 'Kegalle Town', 'phone' => '035-2222275', 'description' => 'Regular waste collection for Kegalle town. Collection days: Monday, Wednesday, Friday.'],
    ['name' => 'Mawanella Urban Council — Waste Collection', 'address' => 'Mawanella Town', 'phone' => '035-2246200', 'description' => 'Waste collection for Mawanella urban area. Collection days: Tuesday, Thursday, Saturday.'],
    ['name' => 'Warakapola Pradeshiya Sabha — Waste', 'address' => 'Warakapola', 'phone' => '035-2267200', 'description' => 'Waste collection for Warakapola area. Contact for schedule details.'],
    ['name' => 'Ruwanwella Pradeshiya Sabha — Waste', 'address' => 'Ruwanwella', 'phone' => '036-2266200', 'description' => 'Waste collection for Ruwanwella area. Contact for schedule details.'],
    ['name' => 'Central Environment Authority — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222800', 'description' => 'Environmental complaints, waste management oversight and recycling programmes.'],
]);

// 8. Road Closures
addItems('road-closures', [
    ['name' => 'Road Development Authority — Kegalle', 'address' => 'Kegalle Town', 'phone' => '035-2222900', 'description' => 'Contact for information about ongoing road construction, repairs and planned closures in the Kegalle district.'],
    ['name' => 'Kegalle Municipal Traffic Updates', 'phone' => '035-2222222', 'description' => 'Contact Kegalle Police for real-time traffic diversions and emergency road closures.'],
    ['name' => 'National Transport Commission — Kegalle', 'phone' => '035-2222950', 'description' => 'Bus route changes, alternative transport arrangements during road closures.'],
]);

// 9. Announcements
addItems('announcements', [
    ['name' => 'District Secretariat — Official Notices', 'address' => 'Kegalle Town', 'phone' => '035-2222235', 'description' => 'Official government announcements, gazette notices and public circulars for Kegalle district.'],
    ['name' => 'Disaster Management Centre — Kegalle', 'phone' => '035-2222117', 'description' => 'Disaster warnings, evacuation notices and emergency alerts. Emergency hotline: 117'],
    ['name' => 'Public Health Inspector — Kegalle', 'phone' => '035-2222261', 'description' => 'Public health advisories, dengue prevention campaigns and vaccination drives.'],
]);

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\nAll done!";
