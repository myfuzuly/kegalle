@extends('layouts.admin')
@section('title','Add Government Service')
@section('page','Government Services')
@section('heading','Add Government Service')
@section('subheading','Create a new service for the Government Services page')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/government-services">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/government-services" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Service Icon Image (optional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. If left empty, the icon emoji + gradient colors are used.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title</label>
        <input name="title" value="{{ old('title') }}" required placeholder="e.g. DS Office, Municipal Council">
    </div>
    <div class="ka-field ka-span-2">
        <label>Short Description</label>
        <textarea name="description" rows="2" placeholder="Brief description shown on the card">{{ old('description') }}</textarea>
    </div>
    <div class="ka-field">
        <label>Icon (emoji, used when no image)</label>
        <input name="icon" value="{{ old('icon', '🏛️') }}" maxlength="10">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
    </div>
    <div class="ka-field">
        <label>Icon Background Start Color</label>
        <input type="color" name="icon_bg_start" value="{{ old('icon_bg_start', '#1e6b3a') }}">
    </div>
    <div class="ka-field">
        <label>Icon Background End Color</label>
        <input type="color" name="icon_bg_end" value="{{ old('icon_bg_end', '#2e9b5a') }}">
    </div>

    <div class="ka-field ka-span-2" style="border-top:1px solid #E5E8EF;padding-top:18px;margin-top:4px">
        <label>Full Page Content (HTML supported)</label>
        <textarea name="content" rows="8" placeholder="Detailed information about this service — HTML is supported">{{ old('content') }}</textarea>
        <small>This content is shown on the individual service page (e.g. /government-services/ds-office).</small>
    </div>

    <div class="ka-field">
        <label>Phone Number(s)</label>
        <input name="phone" value="{{ old('phone') }}" placeholder="037-2222222">
    </div>
    <div class="ka-field">
        <label>Email</label>
        <input name="email" type="email" value="{{ old('email') }}" placeholder="info@example.gov.lk">
    </div>
    <div class="ka-field ka-span-2">
        <label>Address</label>
        <input name="address" value="{{ old('address') }}" placeholder="Main Street, Kegalle">
    </div>
    <div class="ka-field ka-span-2">
        <label>Google Maps Embed URL (optional)</label>
        <input name="map_url" value="{{ old('map_url') }}" placeholder="https://www.google.com/maps/embed?pb=...">
    </div>

    <label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Create Service</button>
    <a class="ka-btn ka-btn-light" href="/admin/government-services">Cancel</a>
</div>
</form>
</section>
@endsection
