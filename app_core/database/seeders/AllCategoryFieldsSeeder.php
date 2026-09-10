<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

/**
 * Seeds category-specific custom fields for all major categories.
 * Run: php artisan db:seed --class=AllCategoryFieldsSeeder --force
 * Safe to re-run — uses updateOrCreate; detaches then re-attaches per category.
 */
class AllCategoryFieldsSeeder extends Seeder
{
    // ── Shared field definitions ─────────────────────────────────────────────

    private function conditionField(int $sort = 10): array
    {
        return [
            'label' => 'Condition', 'name' => 'condition', 'type' => 'select',
            'options' => ['Brand New', 'Used', 'Refurbished', 'Other'],
            'placeholder' => 'Select condition…', 'sort_order' => $sort,
            'is_required' => true, 'show_in_filter' => true, 'show_in_list' => true,
        ];
    }

    private function conditionVehicleField(int $sort = 20): array
    {
        return [
            'label' => 'Condition', 'name' => 'condition', 'type' => 'select',
            'options' => ['Brand New', 'Used', 'Refurbished', 'Other'],
            'placeholder' => 'Select condition…', 'sort_order' => $sort,
            'is_required' => true, 'show_in_filter' => true, 'show_in_list' => true,
        ];
    }

    private function brandField(int $sort = 40): array
    {
        return [
            'label' => 'Brand', 'name' => 'brand_id', 'type' => 'brand_select',
            'sort_order' => $sort, 'is_required' => false,
            'show_in_filter' => true, 'show_in_list' => false,
        ];
    }

    private function modelField(int $sort = 50): array
    {
        return [
            'label' => 'Model', 'name' => 'model_id', 'type' => 'model_select',
            'sort_order' => $sort, 'is_required' => false,
            'show_in_filter' => false, 'show_in_list' => false,
        ];
    }

    private function makeField(int $sort = 30): array
    {
        return [
            'label' => 'Make', 'name' => 'brand_id', 'type' => 'brand_select',
            'sort_order' => $sort, 'is_required' => false,
            'show_in_filter' => true, 'show_in_list' => false,
        ];
    }

    // ── Category definitions ─────────────────────────────────────────────────

    private function categoryMap(): array
    {
        $years = range(date('Y'), 1970);

        return [

            // ── MOBILE ───────────────────────────────────────────────────────
            // Already seeded by MobilePhoneCategoryFieldsSeeder; skip here.

            // ── ELECTRONICS: Computers / Laptops / Tablets ───────────────────
            'computers' => [
                'slugs' => ['computers-648','laptops-651','desktop-computers-650','all-in-one-pcs-649','workstations-654','mini-pcs-652','servers-653'],
                'fields' => [
                    $this->conditionField(10),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Desktop','Laptop','Tablet','Workstation','Mini PC','All-in-One','Other'],
                     'placeholder'=>'Select item type…','sort_order'=>20,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(30),
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Computer Accessories ────────────────────────────
            'computer-accessories' => [
                'slugs' => ['computer-accessories-2','computer-accessories-637','keyboards-mice-638','monitors-639'],
                'fields' => [
                    $this->conditionField(10),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Keyboard','Mouse','Monitor','Printer','Scanner','Web Cam','USB Hub','Hard Drive','SSD','RAM','GPU','CPU','Cooling Fan','UPS','Cable / Adapter','Other'],
                     'placeholder'=>'Select accessory type…','sort_order'=>20,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(30),
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: TV ──────────────────────────────────────────────
            'tv' => [
                'slugs' => ['televisions-2441','televisions-displays-2440','tv-audio-home-entertainment-2439','projectors-2442'],
                'fields' => [
                    $this->conditionField(10),
                    $this->brandField(20),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Smart TV','LED TV','OLED TV','QLED TV','LCD TV','Curved TV','Projector','Other'],
                     'placeholder'=>'Select type…','sort_order'=>30,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    ['label'=>'Screen Type','name'=>'screen_type','type'=>'select',
                     'options'=>['OLED','QLED','LED','LCD','IPS','AMOLED','TFT','Plasma','Other'],
                     'placeholder'=>'Select screen type…','sort_order'=>40,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    ['label'=>'Screen Size','name'=>'screen_size','type'=>'text_unit','unit'=>'inch',
                     'placeholder'=>'55','sort_order'=>50,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    $this->modelField(60),
                ],
            ],

            // ── ELECTRONICS: TV Accessories ──────────────────────────────────
            'tv-accessories' => [
                'slugs' => ['tv-mounts-stands-2443','home-theatre-systems-2444','soundbars-2445','media-players-2447','streaming-devices-2448','set-top-boxes-2449'],
                'fields' => [
                    $this->conditionField(10),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Remote Control','Wall Mount','HDMI Cable','Antenna','Soundbar','Set-Top Box','Streaming Stick','AV Receiver','Other'],
                     'placeholder'=>'Select accessory type…','sort_order'=>20,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(30),
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Camera ──────────────────────────────────────────
            'camera' => [
                'slugs' => ['cameras-photography-899','dslr-cameras-909','mirrorless-cameras-910','action-cameras-900','compact-cameras-908','video-cameras-914'],
                'fields' => [
                    $this->conditionField(10),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['DSLR','Mirrorless','Action Camera','Compact / Point & Shoot','Security / CCTV Camera','Webcam','Drone Camera','Film Camera','Other'],
                     'placeholder'=>'Select camera type…','sort_order'=>20,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(30),
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Audio / MP3 ─────────────────────────────────────
            'audio-mp3' => [
                'slugs' => ['audio-equipment-2450','headphones-earphones-2451','bluetooth-speakers-2452','portable-audio-players-2453','amplifiers-2454','karaoke-equipment-2455'],
                'fields' => [
                    $this->conditionField(10),
                    $this->brandField(20),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Speaker','Headphone','Earphone / AirPods','Amplifier','Home Theater','Soundbar','MP3 Player','Microphone','Turntable','Other'],
                     'placeholder'=>'Select type…','sort_order'=>30,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Electronic Home Appliances ──────────────────────
            'electronic-home-appliances' => [
                'slugs' => ['home-kitchen-appliances-1617','kitchen-appliances-1636','laundry-appliances-1657','refrigeration-water-appliances-1670','cleaning-appliances-1618','small-home-appliances-1679'],
                'fields' => [
                    $this->conditionField(10),
                    $this->brandField(20),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Washing Machine','Refrigerator / Fridge','Microwave','Oven','Iron','Vacuum Cleaner','Blender','Rice Cooker','Water Pump','Sewing Machine','Other'],
                     'placeholder'=>'Select appliance type…','sort_order'=>30,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Video Games & Other Electronics ─────────────────
            'video-games' => [
                'slugs' => ['gaming-consoles-1341','gaming-entertainment-693','gaming-entertainment-1321','gaming-computers-1332','gaming-games-1345'],
                'fields' => [
                    $this->conditionField(10),
                    $this->brandField(20),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Gaming Console','Game Cartridge / Disc','Controller / Joystick','Gaming Chair','Gaming Monitor','VR Headset','Other'],
                     'placeholder'=>'Select type…','sort_order'=>30,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->modelField(40),
                ],
            ],

            // ── ELECTRONICS: Aircon & Fittings ───────────────────────────────
            'aircon' => [
                'slugs' => ['air-conditioners-ac-1627','cooling-air-treatment-1626','portable-air-conditioners-ac-1634','air-coolers-1629','air-purifiers-1630'],
                'fields' => [
                    $this->conditionField(10),
                    $this->brandField(20),
                    ['label'=>'Item Type','name'=>'item_type','type'=>'select',
                     'options'=>['Split AC','Window AC','Portable AC','Inverter AC','Ceiling Fan','Standing Fan','Wall Fan','Exhaust Fan','Cooler','Air Purifier','Other'],
                     'placeholder'=>'Select type…','sort_order'=>30,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    ['label'=>'Capacity','name'=>'screen_size','type'=>'text_unit','unit'=>'BTU',
                     'placeholder'=>'12000','sort_order'=>40,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    $this->modelField(50),
                ],
            ],

            // ── VEHICLES: Cars / SUVs / Vans / Buses / Lorries / Tractor / Boats ──
            'cars' => [
                'slugs' => ['cars-light-vehicles-2611','hatchback-cars-2612','sedan-cars-2613','suvs-jeeps-2614',
                            'vans-mpvs-2615','pickup-trucks-2616','luxury-vehicles-2617','sports-cars-2618',
                            'classic-vintage-cars-2619','three-wheelers-2620','other-vehicles-2622',
                            'commercial-heavy-vehicles-2623','buses-2625','lorries-trucks-2629','dump-trucks-2627',
                            'heavy-machinery-vehicles-2628','mini-trucks-2630','trailers-2631','commercial-vehicles-2632',
                            'marine-watercraft-2643','boats-2644','fishing-boats-2646','speed-boats-2651',
                            'electric-alternative-vehicles-2654','electric-cars-2655','hybrid-cars-2658'],
                'fields' => [
                    ['label'=>'Vehicle Type','name'=>'item_type','type'=>'select',
                     'options'=>['Car','SUV / Jeep','Van','Bus','Lorry / Truck','Three Wheeler','Tractor','Boat','Heavy Machinery','Electric Vehicle','Other'],
                     'placeholder'=>'Select vehicle type…','sort_order'=>10,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->conditionVehicleField(20),
                    $this->makeField(30),
                    $this->modelField(40),
                    ['label'=>'Year of Manufacture','name'=>'year','type'=>'select',
                     'options'=>array_map('strval', range(date('Y'), 1960)),
                     'placeholder'=>'Select year…','sort_order'=>50,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>true],
                    ['label'=>'Mileage','name'=>'mileage','type'=>'text_unit','unit'=>'km',
                     'placeholder'=>'45000','sort_order'=>60,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    ['label'=>'Engine Capacity','name'=>'engine_capacity','type'=>'text_unit','unit'=>'cc',
                     'placeholder'=>'1500','sort_order'=>70,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    ['label'=>'Fuel Type','name'=>'fuel_type','type'=>'select',
                     'options'=>['Petrol','Diesel','Electric','Hybrid (Petrol)','Hybrid (Diesel)','CNG','Other'],
                     'placeholder'=>'Select fuel type…','sort_order'=>80,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    ['label'=>'Transmission','name'=>'transmission','type'=>'select',
                     'options'=>['Manual','Automatic','Semi-Automatic','CVT','Other'],
                     'placeholder'=>'Select transmission…','sort_order'=>90,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                ],
            ],

            // ── VEHICLES: Motorcycles / Bikes ────────────────────────────────
            'motorcycles' => [
                'slugs' => ['motorcycles-scooters-2633','motorcycles-motorbikes-2638','scooters-2641','mopeds-2636','sports-bikes-2639','touring-bikes-2640','atvs-quad-bikes-2634','electric-motorcycles-2635','other-two-wheelers-2642'],
                'fields' => [
                    ['label'=>'Bike Type','name'=>'item_type','type'=>'select',
                     'options'=>['Sport / Racing','Cruiser','Dirt / Off-Road','Scooter','Moped','Adventure','Naked','Electric','Other'],
                     'placeholder'=>'Select bike type…','sort_order'=>10,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->conditionVehicleField(20),
                    $this->makeField(30),
                    $this->modelField(40),
                    ['label'=>'Year of Manufacture','name'=>'year','type'=>'select',
                     'options'=>array_map('strval', range(date('Y'), 1980)),
                     'placeholder'=>'Select year…','sort_order'=>50,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>true],
                    ['label'=>'Mileage','name'=>'mileage','type'=>'text_unit','unit'=>'km',
                     'placeholder'=>'12000','sort_order'=>60,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    ['label'=>'Engine Capacity','name'=>'engine_capacity','type'=>'text_unit','unit'=>'cc',
                     'placeholder'=>'150','sort_order'=>70,'is_required'=>false,
                     'show_in_filter'=>false,'show_in_list'=>false],
                    ['label'=>'Fuel Type','name'=>'fuel_type','type'=>'select',
                     'options'=>['Petrol','Diesel','Electric','Other'],
                     'placeholder'=>'Select fuel type…','sort_order'=>80,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                ],
            ],

            // ── VEHICLES: Bicycles ───────────────────────────────────────────
            'bicycles' => [
                'slugs' => ['bicycles-2602','bmx-bikes-2603','city-bikes-2605','mountain-bikes-2609','road-bikes-2610','electric-bicycles-2607','kids-bicycles-2608','bicycles-500'],
                'fields' => [
                    ['label'=>'Bicycle Type','name'=>'item_type','type'=>'select',
                     'options'=>['Mountain','Road','BMX','City / Hybrid','Folding','Electric','Kids','Other'],
                     'placeholder'=>'Select bicycle type…','sort_order'=>10,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->conditionVehicleField(20),
                    $this->makeField(30),
                    $this->modelField(40),
                ],
            ],

            // ── VEHICLES: Auto Parts & Accessories ───────────────────────────
            'auto-parts' => [
                'slugs' => ['vehicle-parts-2465','vehicle-accessories-2572','body-exterior-parts-2466','engine-drivetrain-2502','suspension-steering-brakes-2561','wheels-tyres-2590','motorcycle-parts-accessories-2541','maintenance-care-2529'],
                'fields' => [
                    $this->conditionField(10),
                    ['label'=>'Part Type','name'=>'item_type','type'=>'select',
                     'options'=>['Engine Part','Body Part','Tyre / Wheel','Battery','Lights','Filters','Brakes','Suspension','Exhaust','Interior','Electronics','Other'],
                     'placeholder'=>'Select part type…','sort_order'=>20,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(30),
                ],
            ],

            // ── VEHICLES: Auto Services / Rentals ────────────────────────────
            'auto-services' => [
                'slugs' => ['automotive-services-2203','vehicle-rental-services-2584','car-rental-2587','vehicle-rental-hire-2350'],
                'fields' => [
                    ['label'=>'Service Type','name'=>'item_type','type'=>'select',
                     'options'=>['Vehicle Rental','Driving School','Repair / Servicing','Towing','Washing / Detailing','Insurance','Other'],
                     'placeholder'=>'Select service type…','sort_order'=>10,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                ],
            ],

            // ── VEHICLES: Maintenance & Repair ───────────────────────────────
            'maintenance-repair' => [
                'slugs' => ['vehicle-repair-maintenance-2212','vehicle-servicing-2213','vehicle-diagnostics-2210','auto-electrical-services-2204','body-repair-painting-2205','car-wash-detailing-2206','tyre-wheel-services-2209'],
                'fields' => [
                    ['label'=>'Service Type','name'=>'item_type','type'=>'select',
                     'options'=>['Engine Repair','Body Repair','Electrical','AC Repair','Tyre Service','Oil Change','General Maintenance','Other'],
                     'placeholder'=>'Select service type…','sort_order'=>10,'is_required'=>false,
                     'show_in_filter'=>true,'show_in_list'=>false],
                    $this->brandField(20),
                ],
            ],

        ];
    }

    // ── Runner ───────────────────────────────────────────────────────────────

    public function run(): void
    {
        $map = $this->categoryMap();
        $seeded = 0;
        $skipped = 0;

        foreach ($map as $key => $def) {
            $category = null;
            foreach ($def['slugs'] as $slug) {
                $category = Category::where('slug', $slug)->first();
                if ($category) break;
            }

            if (! $category) {
                $this->command->warn("[$key] Not found — tried: " . implode(', ', $def['slugs']));
                $skipped++;
                continue;
            }

            // Detach existing custom fields and re-attach fresh
            $category->customFields()->detach();

            foreach ($def['fields'] as $fieldDef) {
                $options = $fieldDef['options'] ?? null;
                // Truncate year arrays for storage (too long = slow query)
                if (is_array($options) && count($options) > 100) {
                    $options = array_slice($options, 0, 80);
                }

                $field = CustomField::updateOrCreate(
                    ['name' => $fieldDef['name'], 'label' => $fieldDef['label']],
                    [
                        'type'         => $fieldDef['type'],
                        'options'      => $options,
                        'placeholder'  => $fieldDef['placeholder'] ?? null,
                        'unit'         => $fieldDef['unit'] ?? null,
                        'multi'        => $fieldDef['multi'] ?? false,
                        'full_width'   => $fieldDef['full_width'] ?? false,
                        'is_required'  => $fieldDef['is_required'] ?? false,
                        'is_searchable'=> $fieldDef['show_in_filter'] ?? false,
                        'sort_order'   => $fieldDef['sort_order'],
                    ]
                );

                $category->customFields()->attach($field->id, [
                    'is_required'    => $fieldDef['is_required'] ?? false,
                    'show_in_filter' => $fieldDef['show_in_filter'] ?? false,
                    'show_in_list'   => $fieldDef['show_in_list'] ?? false,
                    'sort_order'     => $fieldDef['sort_order'],
                ]);
            }

            $this->command->info("[{$key}] Seeded " . count($def['fields']) . " fields → {$category->name} (ID {$category->id})");
            $seeded++;
        }

        $this->command->info("Done. $seeded categories seeded, $skipped skipped (not found in DB).");
        if ($skipped > 0) {
            $this->command->warn("Run: php artisan tinker --execute=\"App\\Models\\Category::pluck('slug','id');\" to list all slugs.");
        }
    }
}
