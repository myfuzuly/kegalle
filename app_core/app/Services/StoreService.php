<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService
{
    public function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            Store::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function create(Request $request, array $validated, User $user): Store
    {
        $data = $this->prepareData($validated, $request);
        $data['user_id'] = $user->id;
        $data['slug']    = $this->generateSlug($data['name']);
        $data['status']  = 'pending';

        $data = $this->handleImages($request, $data, Str::slug($data['name']));

        $store = Store::create($data);

        if ($request->has('categories')) {
            $store->categories()->sync($request->input('categories', []));
        }

        return $store;
    }

    public function update(Request $request, array $validated, Store $store): Store
    {
        $data = $this->prepareData($validated, $request);

        if ($store->name !== $data['name']) {
            $data['slug'] = $this->generateSlug($data['name'], $store->id);
        }

        $storeSlug = Str::slug($data['name'] ?? $store->name);
        $data      = $this->handleImages($request, $data, $storeSlug, $store);

        $store->update($data);
        $store->categories()->sync($request->input('categories', []));

        return $store;
    }

    public static function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw);

        // Strip leading country code to get local 9-digit number
        if (str_starts_with($digits, '94')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Trim to 9 digits if too long (typo protection)
        if (strlen($digits) > 9) {
            $digits = substr($digits, -9);
        }

        return '+94' . $digits;
    }

    public static function normalizeWhatsapp(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw);
        if (str_starts_with($digits, '0')) {
            $digits = '94' . substr($digits, 1);
        }
        if (! str_starts_with($digits, '94')) {
            $digits = '94' . $digits;
        }
        return $digits;
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function prepareData(array $validated, Request $request): array
    {
        $data = $validated;

        if ($request->has('whatsapp_same') && ! empty($data['phone'])) {
            $data['whatsapp'] = $data['phone'];
        }

        unset(
            $data['logo'], $data['banner'],
            $data['remove_logo'], $data['remove_banner'],
            $data['whatsapp_same'], $data['categories']
        );

        if (! empty($data['phone'])) {
            $data['phone'] = self::normalizePhone($data['phone']);
        }
        if (! empty($data['whatsapp'])) {
            $data['whatsapp'] = self::normalizeWhatsapp($data['whatsapp']);
        }

        return $data;
    }

    private function handleImages(Request $request, array $data, string $slug, ?Store $existing = null): array
    {
        // Logo
        if ($request->hasFile('logo')) {
            if ($existing?->logo) {
                Storage::disk('public')->delete($existing->logo);
            }
            $ext         = strtolower($request->file('logo')->getClientOriginalExtension()) ?: 'jpg';
            $data['logo'] = $request->file('logo')->storeAs('stores', "{$slug}-logo-" . time() . ".{$ext}", 'public');
            $data['logo'] = ImageHelper::finalize($data['logo'], false, 500);
        } elseif ($request->boolean('remove_logo') && $existing?->logo) {
            Storage::disk('public')->delete($existing->logo);
            $data['logo'] = null;
        }

        // Banner
        if ($request->hasFile('banner')) {
            if ($existing?->banner) {
                Storage::disk('public')->delete($existing->banner);
            }
            $ext            = strtolower($request->file('banner')->getClientOriginalExtension()) ?: 'jpg';
            $uniqueName     = "{$slug}-banner-" . time() . ".{$ext}";
            $data['banner'] = $request->file('banner')->storeAs('stores', $uniqueName, 'public');
            $posY           = (int) $request->input('banner_position_y', 50);
            ImageHelper::cropToAspect(storage_path("app/public/{$data['banner']}"), 820, 312, $posY);
            $data['banner'] = ImageHelper::finalize($data['banner'], false, 1600);
        } elseif ($request->boolean('remove_banner') && $existing?->banner) {
            Storage::disk('public')->delete($existing->banner);
            $data['banner'] = null;
        }

        return $data;
    }
}
