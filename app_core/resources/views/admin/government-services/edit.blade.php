@extends('layouts.admin')
@section('title','Edit Government Service')
@section('page','Government Services')
@section('heading','Edit Government Service')
@section('subheading','Update this government service entry')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/government-services">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/government-services/{{ $service->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Service Icon Image (optional)</label>
        @if($service->image)
            <div class="flex-row-14-mb10">
                <img class="thumb-80" src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}">
                <label class="text-del-action">
                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                </label>
            </div>
        @else
            <p class="hint-text">No image set — using icon + gradient.</p>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. Uploading replaces the current image.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title</label>
        <input name="title" value="{{ old('title', $service->title) }}" required>
    </div>
    <div class="ka-field ka-span-2">
        <label>Short Description</label>
        <textarea name="description" rows="2">{{ old('description', $service->description) }}</textarea>
    </div>
    <div class="ka-field">
        <label>Icon (emoji, used when no image)</label>
        <input name="icon" value="{{ old('icon', $service->icon) }}" maxlength="10">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', $service->sort_order) }}">
    </div>
    <div class="ka-field">
        <label>Icon Background Start Color</label>
        <input type="color" name="icon_bg_start" value="{{ old('icon_bg_start', $service->icon_bg_start) }}">
    </div>
    <div class="ka-field">
        <label>Icon Background End Color</label>
        <input type="color" name="icon_bg_end" value="{{ old('icon_bg_end', $service->icon_bg_end) }}">
    </div>

    <div class="ka-field ka-span-2 bt-pt18-mt4">
        <label>Full Page Content (HTML supported)</label>
        <textarea name="content" rows="8">{{ old('content', $service->content) }}</textarea>
        <small>This content is shown on the individual service page.</small>
    </div>

    <div class="ka-field">
        <label>Phone Number(s)</label>
        <input name="phone" value="{{ old('phone', $service->phone) }}">
    </div>
    <div class="ka-field">
        <label>Email</label>
        <input name="email" type="email" value="{{ old('email', $service->email) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Address</label>
        <input name="address" value="{{ old('address', $service->address) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Google Maps Embed URL (optional)</label>
        <input name="map_url" value="{{ old('map_url', $service->map_url) }}">
    </div>

    <label class="ka-check"><input type="checkbox" name="is_active" value="1" @checked($service->is_active)> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Service</button>
    <a class="ka-btn ka-btn-light" href="/admin/government-services">Cancel</a>
</div>
</form>
</section>
@endsection
