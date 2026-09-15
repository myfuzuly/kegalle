<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => Setting::getValue('site_name', 'Kegalle'),
            'site_tagline' => Setting::getValue('site_tagline', 'Buy, Sell & Discover'),
            'support_email' => Setting::getValue('support_email', 'support@kegalle.com'),
            'support_phone' => Setting::getValue('support_phone', '+94 713 930 930'),
            'whatsapp_number' => Setting::getValue('whatsapp_number', '94712930930'),
            'default_currency' => Setting::getValue('default_currency', 'LKR'),
            'listing_approval_mode' => Setting::getValue('listing_approval_mode', 'manual'),
            'store_approval_mode' => Setting::getValue('store_approval_mode', 'manual'),
            'max_images_per_listing' => Setting::getValue('max_images_per_listing', '10'),
            'meta_title' => Setting::getValue('meta_title', 'Kegalle Marketplace'),
            'meta_description' => Setting::getValue('meta_description', 'Local products, stores and classified ads in Kegalle.'),
            'maintenance_mode' => Setting::getValue('maintenance_mode', '0'),
            'home_categories_count' => Setting::getValue('home_categories_count', '12'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function toggleMaintenance(Request $request)
    {
        $current = Setting::getValue('maintenance_mode', '0');
        Setting::setValue('maintenance_mode', $current === '1' ? '0' : '1');
        return back()->with('success', $current === '1' ? 'Maintenance mode disabled.' : 'Maintenance mode enabled.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:100',
            'site_tagline' => 'nullable|string|max:180',
            'support_email' => 'nullable|email|max:180',
            'support_phone' => 'nullable|string|max:40',
            'whatsapp_number' => 'nullable|string|max:40',
            'default_currency' => 'required|string|max:10',
            'listing_approval_mode' => 'required|in:auto,manual',
            'store_approval_mode' => 'required|in:auto,manual',
            'max_images_per_listing' => 'required|integer|min:1|max:50',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:300',
            'maintenance_mode' => 'nullable|boolean',
            'home_categories_count' => 'required|integer|min:6|max:60',
        ]);

        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';

        foreach ($data as $key => $value) {
            Setting::setValue($key, (string) $value);
        }

        return back()->with('success', 'Site settings updated successfully.');
    }
}
