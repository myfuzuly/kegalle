<?php
$lockFile = __DIR__ . '/seed_events2.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

$existing = Event::pluck('slug')->toArray();

$events = [
    [
        'title' => 'Kegalle Food Carnival 2025',
        'slug' => 'kegalle-food-carnival-2025',
        'description' => 'Over 50 food vendors, cooking competitions, live music, kids activities and cultural performances. Free entry for all ages.',
        'event_date' => '2026-07-12',
        'starts_at' => '2026-07-12 11:00:00',
        'ends_at' => '2026-07-12 22:00:00',
        'event_type' => 'offline',
        'venue' => 'Mawanella Road Grounds',
        'location' => 'Mawanella',
        'price' => 0,
        'is_free' => true,
        'is_featured' => true,
        'status' => 'published',
        'organizer_name' => 'Kegalle Food Association',
    ],
    [
        'title' => 'Cricket Tournament 2025',
        'slug' => 'cricket-tournament-2025',
        'description' => 'Annual inter-district cricket tournament featuring 16 teams from across the Western and Sabaragamuwa provinces.',
        'event_date' => '2026-07-16',
        'starts_at' => '2026-07-16 08:00:00',
        'ends_at' => '2026-07-16 18:00:00',
        'event_type' => 'offline',
        'venue' => 'Warakapola Stadium',
        'location' => 'Warakapola',
        'price' => 800,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Warakapola Cricket Association',
    ],
    [
        'title' => 'Photography Masterclass',
        'slug' => 'photography-masterclass',
        'description' => 'Comprehensive masterclass covering landscape photography, portraiture, editing techniques and composition. Hands-on outdoor shooting sessions.',
        'event_date' => '2026-07-19',
        'starts_at' => '2026-07-19 10:00:00',
        'ends_at' => '2026-07-19 16:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle Cultural Center',
        'location' => 'Kegalle',
        'price' => 3000,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Lanka Photography Guild',
    ],
    [
        'title' => 'Buddhist Vesak Festival',
        'slug' => 'buddhist-vesak-festival',
        'description' => 'Beautiful lantern displays, Vesak pandols, dansalas and traditional cultural performances. A spiritual gathering for all ages.',
        'event_date' => '2026-07-22',
        'starts_at' => '2026-07-22 17:00:00',
        'ends_at' => '2026-07-22 21:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle Town',
        'location' => 'Kegalle',
        'price' => 0,
        'is_free' => true,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Kegalle Buddhist Association',
    ],
    [
        'title' => 'Women in Business Summit',
        'slug' => 'women-in-business-summit',
        'description' => 'Empowering women entrepreneurs with keynote speeches, panel discussions, networking sessions and mentorship matching.',
        'event_date' => '2026-08-02',
        'starts_at' => '2026-08-02 09:00:00',
        'ends_at' => '2026-08-02 17:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle Business Hub',
        'location' => 'Kegalle',
        'price' => 2000,
        'is_free' => false,
        'is_featured' => true,
        'status' => 'published',
        'organizer_name' => 'Women Entrepreneurs Network SL',
    ],
    [
        'title' => 'Kids Summer Workshop',
        'slug' => 'kids-summer-workshop',
        'description' => 'Fun-filled summer workshop for kids aged 5-12. Arts & crafts, science experiments, storytelling, dance and outdoor games.',
        'event_date' => '2026-08-06',
        'starts_at' => '2026-08-06 09:00:00',
        'ends_at' => '2026-08-06 12:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle Community Hall',
        'location' => 'Kegalle',
        'price' => 1200,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Kegalle Youth Development Center',
    ],
];

$count = 0;
foreach ($events as $data) {
    if (in_array($data['slug'], $existing)) {
        echo "Skipped (exists): {$data['title']}\n";
        continue;
    }
    Event::create($data);
    echo "Created: {$data['title']}\n";
    $count++;
}

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\nDone! $count new events created.";
