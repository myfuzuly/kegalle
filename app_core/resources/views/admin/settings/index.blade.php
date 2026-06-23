@extends('layouts.admin')

@section('title','Site Settings')
@section('page','Settings')
@section('heading','Site Settings')
@section('subheading','Manage marketplace branding, approvals, SEO and support information')

@section('content')
<section class="sa-card">
    <form class="ka-premium-form" method="post" action="/admin/settings">
        @csrf

        <div class="ka-settings-section">
            <h2>Brand & Contact</h2>
            <div class="ka-form-grid">
                <div class="ka-field">
                    <label>Site Name</label>
                    <input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Kegalle') }}" required>
                </div>

                <div class="ka-field">
                    <label>Site Tagline</label>
                    <input name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                </div>

                <div class="ka-field">
                    <label>Support Email</label>
                    <input name="support_email" type="email" value="{{ old('support_email', $settings['support_email'] ?? '') }}">
                </div>

                <div class="ka-field">
                    <label>Support Phone</label>
                    <input name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}">
                </div>

                <div class="ka-field">
                    <label>WhatsApp Number</label>
                    <input name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}">
                </div>

                <div class="ka-field">
                    <label>Default Currency</label>
                    <input name="default_currency" value="{{ old('default_currency', $settings['default_currency'] ?? 'LKR') }}" required>
                </div>
            </div>
        </div>

        <div class="ka-settings-section">
            <h2>Marketplace Rules</h2>
            <div class="ka-form-grid">
                <div class="ka-field">
                    <label>Listing Approval</label>
                    <select name="listing_approval_mode">
                        <option value="manual" @selected(($settings['listing_approval_mode'] ?? '') === 'manual')>Manual Approval</option>
                        <option value="auto" @selected(($settings['listing_approval_mode'] ?? '') === 'auto')>Auto Approval</option>
                    </select>
                </div>

                <div class="ka-field">
                    <label>Store Approval</label>
                    <select name="store_approval_mode">
                        <option value="manual" @selected(($settings['store_approval_mode'] ?? '') === 'manual')>Manual Approval</option>
                        <option value="auto" @selected(($settings['store_approval_mode'] ?? '') === 'auto')>Auto Approval</option>
                    </select>
                </div>

                <div class="ka-field">
                    <label>Max Images Per Listing</label>
                    <input name="max_images_per_listing" type="number" value="{{ old('max_images_per_listing', $settings['max_images_per_listing'] ?? 10) }}">
                </div>

                <label class="ka-check">
                    <input type="checkbox" name="maintenance_mode" value="1" @checked(($settings['maintenance_mode'] ?? '0') === '1')>
                    Maintenance Mode
                </label>
            </div>
        </div>

        <div class="ka-settings-section">
            <h2>SEO Defaults</h2>
            <div class="ka-form-grid">
                <div class="ka-field">
                    <label>Meta Title</label>
                    <input name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}">
                </div>

                <div class="ka-field ka-span-2">
                    <label>Meta Description</label>
                    <textarea name="meta_description">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="ka-form-actions">
            <button class="ka-btn ka-btn-primary">Save Settings</button>
        </div>
    </form>
</section>
@endsection
