<?php
$lockFile = __DIR__ . '/seed_events.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use Illuminate\Support\Str;

if (Event::count() > 0) { die('Events already exist.'); }

$events = [
    [
        'title' => 'Kegalle Music Festival 2025',
        'slug' => 'kegalle-music-festival-2025',
        'description' => 'Join us for the biggest music festival in Kegalle district featuring top Sri Lankan artists, live bands and cultural performances.',
        'event_date' => '2026-07-05',
        'starts_at' => '2026-07-05 18:00:00',
        'ends_at' => '2026-07-05 23:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle City Ground',
        'location' => 'Kegalle',
        'price' => 1500,
        'is_free' => false,
        'is_featured' => true,
        'status' => 'published',
        'organizer_name' => 'Kegalle Events Committee',
    ],
    [
        'title' => 'Digital Marketing Workshop',
        'slug' => 'digital-marketing-workshop',
        'description' => 'Learn social media marketing, SEO and content strategy from industry experts. Perfect for local business owners.',
        'event_date' => '2026-07-08',
        'starts_at' => '2026-07-08 09:00:00',
        'ends_at' => '2026-07-08 13:00:00',
        'event_type' => 'offline',
        'venue' => 'Kegalle Business Hub',
        'location' => 'Kegalle',
        'price' => 2500,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Kegalle Chamber of Commerce',
    ],
    [
        'title' => 'Pinnawala Cultural Fair',
        'slug' => 'pinnawala-cultural-fair',
        'description' => 'Annual cultural fair celebrating Sabaragamuwa heritage with traditional dance, drumming, crafts and local cuisine.',
        'event_date' => '2026-07-12',
        'starts_at' => '2026-07-12 10:00:00',
        'ends_at' => '2026-07-12 20:00:00',
        'event_type' => 'offline',
        'venue' => 'Pinnawala Community Hall',
        'location' => 'Rambukkana',
        'price' => 0,
        'is_free' => true,
        'is_featured' => true,
        'status' => 'published',
        'organizer_name' => 'Pinnawala Cultural Society',
    ],
    [
        'title' => 'Kegalle Entrepreneurs Meetup',
        'slug' => 'kegalle-entrepreneurs-meetup',
        'description' => 'Networking event for Kegalle district entrepreneurs. Share ideas, find partners and grow your business.',
        'event_date' => '2026-07-16',
        'starts_at' => '2026-07-16 17:00:00',
        'ends_at' => '2026-07-16 20:00:00',
        'event_type' => 'offline',
        'venue' => 'Hotel Grand Kegalle',
        'location' => 'Kegalle',
        'price' => 500,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Startup Kegalle',
    ],
    [
        'title' => 'Mawanella Food Festival',
        'slug' => 'mawanella-food-festival',
        'description' => 'A celebration of local flavours — spice-infused dishes, street food, traditional sweets and cooking demonstrations.',
        'event_date' => '2026-07-20',
        'starts_at' => '2026-07-20 11:00:00',
        'ends_at' => '2026-07-20 21:00:00',
        'event_type' => 'offline',
        'venue' => 'Mawanella Town Hall',
        'location' => 'Mawanella',
        'price' => 0,
        'is_free' => true,
        'is_featured' => true,
        'status' => 'published',
        'organizer_name' => 'Mawanella Traders Association',
    ],
    [
        'title' => 'Yoga & Wellness Retreat',
        'slug' => 'yoga-wellness-retreat-kegalle',
        'description' => 'Weekend wellness retreat in the hills of Aranayake with yoga sessions, meditation and nature walks.',
        'event_date' => '2026-07-26',
        'starts_at' => '2026-07-26 06:00:00',
        'ends_at' => '2026-07-27 16:00:00',
        'event_type' => 'offline',
        'venue' => 'Aranayake Eco Lodge',
        'location' => 'Aranayake',
        'price' => 5000,
        'is_free' => false,
        'is_featured' => false,
        'status' => 'published',
        'organizer_name' => 'Kegalle Wellness Hub',
    ],
];

foreach ($events as $data) {
    Event::create($data);
    echo "Created: {$data['title']}\n";
}

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\nAll done! " . count($events) . " events seeded.";
