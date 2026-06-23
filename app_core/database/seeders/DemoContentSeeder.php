<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Location;
use App\Models\MembershipPlan;
use App\Models\Post;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@kegalle.lk'],
            ['name' => 'Super Admin', 'phone' => '+94771234567', 'password' => Hash::make('Password123@'), 'role' => 'super_admin', 'status' => 'active', 'account_type' => 'store', 'email_verified_at' => now()]
        );

        MembershipPlan::firstOrCreate(['slug' => 'free'], ['name' => 'Free', 'price' => 0, 'duration_days' => 30, 'ad_limit' => 5, 'product_limit' => 10, 'store_limit' => 1, 'featured_quota' => 0, 'is_active' => 1]);
        MembershipPlan::firstOrCreate(['slug' => 'premium-store'], ['name' => 'Premium Store', 'price' => 2500, 'duration_days' => 30, 'ad_limit' => 50, 'product_limit' => 200, 'store_limit' => 1, 'featured_quota' => 10, 'is_active' => 1]);

        $locations = collect(['Kegalle', 'Mawanella', 'Ruwanwella', 'Aranayake', 'Yatiyanthota', 'Warakapola', 'Dehiowita'])->mapWithKeys(fn ($name) => [
            $name => Location::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'type' => 'city', 'is_active' => 1]),
        ]);

        $categoriesData = [
            ['Electronics', 'electronics', '📱'], ['Vehicles', 'vehicles', '🚗'], ['Property', 'property', '🏠'], ['Home & Garden', 'home-garden', '🪴'], ['Fashion', 'fashion', '👕'], ['Jobs', 'jobs', '💼'], ['Services', 'services', '🔧'], ['Sports & Hobbies', 'sports-hobbies', '⚽'],
        ];
        $categories = collect($categoriesData)->mapWithKeys(fn ($c) => [
            $c[1] => Category::firstOrCreate(['slug' => $c[1]], ['name' => $c[0], 'icon' => $c[2], 'type' => 'both', 'is_active' => 1]),
        ]);

        $storesData = [
            ['Tech World Sri Lanka', 'Electronics', 'Kegalle', 'demo/store-tech.svg'],
            ['Auto Express Kegalle', 'Vehicles', 'Mawanella', 'demo/store-auto.svg'],
            ['Home Essentials', 'Home & Garden', 'Ruwanwella', 'demo/store-home.svg'],
            ['Fashion Hub Kegalle', 'Fashion', 'Kegalle', 'demo/store-fashion.svg'],
            ['Electro Mart Kegalle', 'Electronics', 'Warakapola', 'demo/store-electro.svg'],
        ];
        $stores = collect($storesData)->map(function ($s) use ($admin) {
            return Store::firstOrCreate(['slug' => Str::slug($s[0])], [
                'user_id' => $admin->id, 'name' => $s[0], 'logo' => $s[3], 'banner' => 'demo/store-cover.svg', 'description' => 'Trusted local seller offering quality products and services in Kegalle.', 'email' => strtolower(str_replace(' ', '', $s[0])).'@kegalle.lk', 'phone' => '+94 77 123 4567', 'whatsapp' => '+94 77 123 4567', 'address' => $s[2].', Kegalle', 'city' => $s[2], 'status' => 'approved', 'is_featured' => 1,
            ]);
        });

        $items = [
            ['iPhone 13 Pro Max 256GB', 'electronics', 'Tech World Sri Lanka', 'Kegalle', 235000, 'product', 1, 'demo/iphone.svg'],
            ['Dell Core i5 Laptop 8GB RAM', 'electronics', 'Tech World Sri Lanka', 'Aranayake', 85000, 'product', 1, 'demo/laptop.svg'],
            ['Toyota Aqua 2018 Hybrid', 'vehicles', 'Auto Express Kegalle', 'Kegalle', 6250000, 'classified', 1, 'demo/car.svg'],
            ['Modern Apartment for Rent in Kegalle', 'property', 'Home Essentials', 'Kegalle', 45000, 'classified', 1, 'demo/apartment.svg'],
            ['Canon EOS 750D DSLR Camera', 'electronics', 'Tech World Sri Lanka', 'Mawanella', 120000, 'product', 0, 'demo/camera.svg'],
            ['Samsung Galaxy Note 9', 'electronics', 'Electro Mart Kegalle', 'Ruwanwella', 72000, 'product', 0, 'demo/phone.svg'],
            ['Apple Watch Series 6', 'electronics', 'Electro Mart Kegalle', 'Warakapola', 35000, 'product', 0, 'demo/watch.svg'],
            ['Sony WH-1000XM4 Headphones', 'electronics', 'Tech World Sri Lanka', 'Kegalle', 48000, 'product', 0, 'demo/headphones.svg'],
            ['Nike Running Shoes', 'fashion', 'Fashion Hub Kegalle', 'Aranayake', 18500, 'product', 0, 'demo/shoes.svg'],
            ['Team Jersey - Original', 'fashion', 'Fashion Hub Kegalle', 'Kegalle', 3500, 'product', 0, 'demo/jersey.svg'],
            ['Travel Backpack 50L', 'sports-hobbies', 'Home Essentials', 'Mawanella', 8900, 'product', 0, 'demo/bag.svg'],
            ['Ultrabook i7 16GB RAM 512GB SSD', 'electronics', 'Tech World Sri Lanka', 'Kegalle', 155000, 'product', 0, 'demo/ultrabook.svg'],
            ['2 Bedroom House for Rent', 'property', 'Home Essentials', 'Kegalle', 28000, 'classified', 0, 'demo/house.svg'],
            ['Second Hand Bike', 'vehicles', 'Auto Express Kegalle', 'Kegalle', 145000, 'classified', 0, 'demo/bike.svg'],
            ['English Tuition Classes', 'services', 'Home Essentials', 'Kegalle', 2000, 'classified', 0, 'demo/class.svg'],
        ];

        foreach ($items as $i) {
            $category = $categories[$i[1]] ?? $categories->first();
            $store = $stores->firstWhere('name', $i[2]);
            $location = $locations[$i[3]] ?? $locations->first();
            $listing = Listing::firstOrCreate(['slug' => Str::slug($i[0])], [
                'user_id' => $admin->id, 'store_id' => optional($store)->id, 'category_id' => optional($category)->id, 'location_id' => optional($location)->id, 'ad_type' => $i[5] === 'classified' ? 'sell' : 'sale', 'type' => $i[5], 'title' => $i[0], 'description' => $i[0].' in excellent condition. Contact seller for more details. Available in '.$i[3].'.', 'price' => $i[4], 'currency' => 'LKR', 'condition' => 'Used - Like New', 'location' => $i[3], 'status' => 'approved', 'is_featured' => $i[6], 'views' => rand(15, 250),
            ]);
            ListingImage::firstOrCreate(['listing_id' => $listing->id, 'path' => $i[7]], ['sort_order' => 0]);
        }

        foreach ([
            'Top 10 Places to Visit in Kegalle', 'Best Local Restaurants in Kegalle', 'Kegalle Travel Guide 2025', 'Historic Places Around Kegalle', 'Best Shopping Areas in Kegalle',
        ] as $postTitle) {
            Post::firstOrCreate(['slug' => Str::slug($postTitle)], ['title' => $postTitle, 'image' => 'demo/blog.svg', 'excerpt' => 'Explore local highlights, places and services around Kegalle.', 'body' => 'Guide content for '.$postTitle, 'is_published' => 1, 'published_at' => now()]);
        }
    }
}
