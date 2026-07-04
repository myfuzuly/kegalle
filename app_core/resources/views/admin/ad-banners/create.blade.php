@extends('layouts.admin')
@section('title','Add Ad Banner')
@section('page','Ad Spaces')
@section('heading','Add Ad Banner')
@section('subheading','Create a new sponsored banner and assign it to a placement location')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/ad-banners">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/ad-banners" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Banner Image</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. Use a size matching the location's recommended dimensions (shown in the dropdown).</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title / Advertiser Name</label>
        <input name="title" value="{{ old('title') }}" required>
    </div>
    <div class="ka-field">
        <label>Placement Location</label>
        <select name="location" required>
            <option value="">Select Location</option>
            @foreach($locations as $key => $label)
                <option value="{{ $key }}" @selected(old('location') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="ka-field">
        <label>Click-through Link (optional)</label>
        <input type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://...">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
        <small>Lower numbers show first when multiple banners share a location.</small>
    </div>
    <div class="ka-field">
        <label>Starts On (optional)</label>
        <input type="date" name="starts_at" value="{{ old('starts_at') }}">
    </div>
    <div class="ka-field">
        <label>Ends On (optional)</label>
        <input type="date" name="ends_at" value="{{ old('ends_at') }}">
        <small>Leave both dates empty to run indefinitely.</small>
    </div>
    <label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Create Ad Banner</button>
    <a class="ka-btn ka-btn-light" href="/admin/ad-banners">Cancel</a>
</div>
</form>
</section>
@endsection
