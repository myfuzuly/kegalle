@extends('layouts.admin')
@section('title','Edit Item — ' . $service->title)
@section('page','Government Services')
@section('heading','Edit Item — ' . $item->name)
@section('subheading','Update this entry under ' . $service->title)
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/government-services/{{ $service->id }}/items">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/government-services/{{ $service->id }}/items/{{ $item->id }}" enctype="multipart/form-data">
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
            <div class="flex-row-14-mb10">
                <img class="thumb-80x60" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}">
                <label class="text-del-action">
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
    <div class="ka-field flex-end">
        <label class="ka-check"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Item</button>
    <a class="ka-btn ka-btn-light" href="/admin/government-services/{{ $service->id }}/items">Cancel</a>
</div>
</form>
</section>
@endsection
