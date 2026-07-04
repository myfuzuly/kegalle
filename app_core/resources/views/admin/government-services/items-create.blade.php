@extends('layouts.admin')
@section('title','Add Item — ' . $service->title)
@section('page','Government Services')
@section('heading','Add Item to ' . $service->title)
@section('subheading','Create a new entry under ' . $service->title)
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/government-services/{{ $service->id }}/items">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/government-services/{{ $service->id }}/items" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Name</label>
        <input name="name" value="{{ old('name') }}" required placeholder="e.g. Kegalle Divisional Secretariat">
    </div>
    <div class="ka-field ka-span-2">
        <label>Description (optional)</label>
        <textarea name="description" rows="2" placeholder="Brief description or additional details">{{ old('description') }}</textarea>
    </div>
    <div class="ka-field">
        <label>Phone Number(s)</label>
        <input name="phone" value="{{ old('phone') }}" placeholder="035-2222222">
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
    <div class="ka-field ka-span-2">
        <label>Image (optional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB.</small>
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
    </div>
    <div class="ka-field" style="display:flex;align-items:flex-end">
        <label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Add Item</button>
    <a class="ka-btn ka-btn-light" href="/admin/government-services/{{ $service->id }}/items">Cancel</a>
</div>
</form>
</section>
@endsection
