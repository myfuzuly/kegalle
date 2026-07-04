@extends('layouts.admin')
@section('title','Edit Ad Banner')
@section('page','Ad Spaces')
@section('heading','Edit Ad Banner')
@section('subheading','Update banner image, placement, schedule and status')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/ad-banners">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/ad-banners/{{ $banner->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Banner Image</label>
        @if($banner->image)
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" style="width:140px;height:auto;border-radius:10px;border:1.5px solid var(--ka-border,#E5E8EF)">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                </label>
            </div>
        @else
            <p style="font-size:13px;color:#667085;margin-bottom:8px">No image uploaded yet.</p>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. Uploading a new image replaces the current one.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title / Advertiser Name</label>
        <input name="title" value="{{ old('title', $banner->title) }}" required>
    </div>
    <div class="ka-field">
        <label>Placement Location</label>
        <select name="location" required>
            <option value="">Select Location</option>
            @foreach($locations as $key => $label)
                <option value="{{ $key }}" @selected(old('location', $banner->location) === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="ka-field">
        <label>Click-through Link (optional)</label>
        <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://...">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', $banner->sort_order) }}">
    </div>
    <div class="ka-field">
        <label>Starts On (optional)</label>
        <input type="date" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}">
    </div>
    <div class="ka-field">
        <label>Ends On (optional)</label>
        <input type="date" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}">
    </div>
    <div class="ka-field">
        <label>Clicks Recorded</label>
        <input value="{{ number_format($banner->clicks) }}" disabled>
    </div>
    <label class="ka-check"><input type="checkbox" name="is_active" value="1" @checked($banner->is_active)> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Ad Banner</button>
    <a class="ka-btn ka-btn-light" href="/admin/ad-banners">Cancel</a>
</div>
</form>
</section>
@endsection
