@extends('layouts.admin')
@section('title','Edit Item — ' . $explore->title)
@section('page','Explore Kegalle')
@section('heading','Edit Item — ' . $item->name)
@section('subheading','Update this entry under ' . $explore->title)
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/explore-items/{{ $explore->id }}/items">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/explore-items/{{ $explore->id }}/items/{{ $item->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Name</label>
        <input name="name" value="{{ old('name', $item->name) }}" required>
    </div>
    <div class="ka-field ka-span-2">
        <label>Description (optional)</label>
        <textarea name="description" rows="2">{{ old('description', $item->description) }}</textarea>
    </div>
    <div class="ka-field">
        <label>Phone Number(s)</label>
        <input name="phone" value="{{ old('phone', $item->phone) }}">
    </div>
    <div class="ka-field">
        <label>Email</label>
        <input name="email" type="email" value="{{ old('email', $item->email) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Address</label>
        <input name="address" value="{{ old('address', $item->address) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Google Maps Embed URL (optional)</label>
        <input name="map_url" value="{{ old('map_url', $item->map_url) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Image (optional)</label>
        @if($item->image)
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" style="width:80px;height:60px;border-radius:10px;object-fit:cover;border:1.5px solid var(--ka-border,#E5E8EF)">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                    <input type="checkbox" name="remove_image" value="1"> Remove image
                </label>
            </div>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB.</small>
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', $item->sort_order) }}">
    </div>
    <div class="ka-field" style="display:flex;align-items:flex-end">
        <label class="ka-check"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Item</button>
    <a class="ka-btn ka-btn-light" href="/admin/explore-items/{{ $explore->id }}/items">Cancel</a>
</div>
</form>
</section>
@endsection
