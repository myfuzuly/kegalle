<?php
$lockFile = __DIR__ . '/seed_gov_services.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GovernmentService;

$services = [
    [
        'title' => 'DS Office',
        'slug' => 'ds-office',
        'description' => 'Divisional Secretariat services — birth/death certificates, NIC, land permits and more.',
        'icon' => '🏛️',
        'icon_bg_start' => '#1e6b3a',
        'icon_bg_end' => '#2e9b5a',
        'sort_order' => 1,
    ],
    [
        'title' => 'Municipal Council',
        'slug' => 'municipal-council',
        'description' => 'Local council services — building permits, trade licenses, town planning and civic issues.',
        'icon' => '🏗️',
        'icon_bg_start' => '#1a5276',
        'icon_bg_end' => '#2980b9',
        'sort_order' => 2,
    ],
    [
        'title' => 'Police',
        'slug' => 'police',
        'description' => 'Police stations, contact numbers, filing complaints and emergency information.',
        'icon' => '🛡️',
        'icon_bg_start' => '#2c3e50',
        'icon_bg_end' => '#4a6785',
        'sort_order' => 3,
    ],
    [
        'title' => 'Hospitals',
        'slug' => 'hospitals',
        'description' => 'Government hospitals, clinics, emergency contacts and health services in Kegalle.',
        'icon' => '🏥',
        'icon_bg_start' => '#c0392b',
        'icon_bg_end' => '#e74c3c',
        'sort_order' => 4,
    ],
    [
        'title' => 'Schools',
        'slug' => 'schools',
        'description' => 'Government schools, education zonal offices, enrolment information and contacts.',
        'icon' => '🎓',
        'icon_bg_start' => '#e67e22',
        'icon_bg_end' => '#f39c12',
        'sort_order' => 5,
    ],
    [
        'title' => 'Public Services',
        'slug' => 'public-services',
        'description' => 'Samurdhi, pensions, social services, Grama Niladhari contacts and public utilities.',
        'icon' => '👥',
        'icon_bg_start' => '#16a085',
        'icon_bg_end' => '#1abc9c',
        'sort_order' => 6,
    ],
    [
        'title' => 'Waste Collection',
        'slug' => 'waste-collection',
        'description' => 'Garbage collection schedules, recycling centres and environmental services.',
        'icon' => '🗑️',
        'icon_bg_start' => '#27ae60',
        'icon_bg_end' => '#2ecc71',
        'sort_order' => 7,
    ],
    [
        'title' => 'Road Closures',
        'slug' => 'road-closures',
        'description' => 'Current road closures, diversions, repair work and traffic updates in Kegalle.',
        'icon' => '⚠️',
        'icon_bg_start' => '#d35400',
        'icon_bg_end' => '#e67e22',
        'sort_order' => 8,
    ],
    [
        'title' => 'Announcements',
        'slug' => 'announcements',
        'description' => 'Official government announcements, public notices and important updates for Kegalle.',
        'icon' => '📢',
        'icon_bg_start' => '#8e44ad',
        'icon_bg_end' => '#9b59b6',
        'sort_order' => 9,
    ],
];

foreach ($services as $s) {
    GovernmentService::create(array_merge($s, ['is_active' => true]));
}

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo count($services) . ' government services seeded successfully.';
