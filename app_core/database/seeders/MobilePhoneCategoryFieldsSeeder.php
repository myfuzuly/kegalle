<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

/**
 * Seeds category-specific custom fields for the Mobile Phones category.
 *
 * Run: php artisan db:seed --class=MobilePhoneCategoryFieldsSeeder
 *
 * The seeder is idempotent — re-running it updates existing fields rather
 * than creating duplicates.
 */
class MobilePhoneCategoryFieldsSeeder extends Seeder
{
    public function run(): void
    {
        // Find the mobile/smartphones category (try several slug variations)
        $slugs = ['mobile-phones-tablets-2015', 'smartphones', 'mobile-phones', 'mobile', 'phones'];
        $category = null;
        foreach ($slugs as $slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) break;
        }

        if (! $category) {
            $this->command->warn('Mobile phone category not found. Tried slugs: ' . implode(', ', $slugs));
            $this->command->warn('Create the category first, then re-run this seeder.');
            return;
        }

        $fields = [
            // 1. Condition — dropdown (rendered as searchable select)
            [
                'label' => 'Condition',
                'name'  => 'condition',
                'type'  => 'select',
                'options' => ['Brand New', 'Used', 'Refurbished', 'For Parts', 'Other'],
                'placeholder' => 'Select condition…',
                'sort_order' => 10,
                'is_required' => true,
            ],
            // 2. Features — checkbox group (full-width)
            [
                'label' => 'Features',
                'name'  => 'features',
                'type'  => 'checkbox_group',
                'options' => [
                    'USB Type-B Port',
                    'USB Type-C Port',
                    'Fast Charging',
                    'Flash Charging',
                    'Expandable Memory',
                    'Bluetooth',
                    'WiFi',
                    'GPS',
                    'Fingerprint Sensor',
                    'Infrared Port',
                ],
                'full_width' => true,
                'sort_order' => 20,
                'is_required' => false,
            ],
            // 3. RAM — pill group (single select)
            [
                'label' => 'RAM',
                'name'  => 'ram',
                'type'  => 'pill_group',
                'options' => ['2 GB', '3 GB', '4 GB', '6 GB', '8 GB', '12 GB', '16 GB'],
                'sort_order' => 30,
                'is_required' => false,
            ],
            // 4. Storage — pill group (single select)
            [
                'label' => 'Storage',
                'name'  => 'storage',
                'type'  => 'pill_group',
                'options' => ['8 GB', '16 GB', '32 GB', '64 GB', '128 GB', '256 GB', '512 GB', '1 TB'],
                'sort_order' => 40,
                'is_required' => false,
            ],
            // 5. Network — pill group (multi-select)
            [
                'label' => 'Network',
                'name'  => 'network',
                'type'  => 'pill_group',
                'options' => ['2G', '3G', '4G', '5G'],
                'multi' => true,
                'sort_order' => 50,
                'is_required' => false,
            ],
            // 6. SIM Support — pill group (single select)
            [
                'label' => 'SIM Support',
                'name'  => 'sim_support',
                'type'  => 'pill_group',
                'options' => ['Single SIM', 'Dual SIM', '1 eSIM + 1 Physical SIM'],
                'sort_order' => 60,
                'is_required' => false,
            ],
            // 7. Main Camera — number with unit MP
            [
                'label' => 'Main Camera',
                'name'  => 'main_camera',
                'type'  => 'text_unit',
                'unit'  => 'MP',
                'placeholder' => '48',
                'sort_order' => 70,
                'is_required' => false,
            ],
            // 8. Screen Size — number with unit inch
            [
                'label' => 'Screen Size',
                'name'  => 'screen_size',
                'type'  => 'text_unit',
                'unit'  => 'inch',
                'placeholder' => '6.5',
                'sort_order' => 80,
                'is_required' => false,
            ],
            // 9. Battery — number with unit mAh
            [
                'label' => 'Battery',
                'name'  => 'battery',
                'type'  => 'text_unit',
                'unit'  => 'mAh',
                'placeholder' => '5000',
                'sort_order' => 90,
                'is_required' => false,
            ],
        ];

        // Detach all existing custom fields from this category first
        $category->customFields()->detach();

        foreach ($fields as $idx => $def) {
            // Upsert the field by name
            $field = CustomField::updateOrCreate(
                ['name' => $def['name']],
                [
                    'label'       => $def['label'],
                    'type'        => $def['type'],
                    'options'     => $def['options'] ?? null,
                    'placeholder' => $def['placeholder'] ?? null,
                    'unit'        => $def['unit'] ?? null,
                    'multi'       => $def['multi'] ?? false,
                    'full_width'  => $def['full_width'] ?? false,
                    'is_required' => $def['is_required'] ?? false,
                    'is_searchable' => false,
                    'sort_order'  => $def['sort_order'],
                ]
            );

            // Attach to the mobile category with pivot data
            $category->customFields()->attach($field->id, [
                'is_required'   => $def['is_required'] ?? false,
                'show_in_filter' => in_array($def['name'], ['condition', 'ram', 'storage', 'network']),
                'show_in_list'  => in_array($def['name'], ['condition', 'ram']),
                'sort_order'    => $def['sort_order'],
            ]);
        }

        $this->command->info("Seeded " . count($fields) . " custom fields for category: {$category->name} (ID {$category->id})");
    }
}
