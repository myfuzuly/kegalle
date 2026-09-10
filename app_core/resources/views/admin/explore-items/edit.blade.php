@extends('layouts.admin')
@section('title','Edit Explore Card')
@section('page','Explore Kegalle')
@section('heading','Edit Explore Card')
@section('subheading','Update this "Explore in Kegalle" homepage card')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/explore-items">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/explore-items/{{ $item->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Card Photo (optional)</label>
        @if($item->image)
            <div class="flex-row-14-mb10">
                <img class="thumb-120x90" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
                <label class="text-del-action">
                    <input type="checkbox" name="remove_image" value="1"> Remove current photo
                </label>
            </div>
        @else
            <p class="hint-text">No photo set — using icon + gradient.</p>
        @endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. Uploading replaces the current photo.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title</label>
        <input name="title" value="{{ old('title', $item->title) }}" required>
    </div>
    <div class="ka-field">
        <label>Icon (emoji, used when no photo is set)</label>
        <input name="icon" value="{{ old('icon', $item->icon) }}" maxlength="10">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', $item->sort_order) }}">
    </div>
    <div class="ka-field">
        <label>Gradient Start Color</label>
        <input type="color" name="gradient_start" value="{{ old('gradient_start', $item->gradient_start) }}">
    </div>
    <div class="ka-field">
        <label>Gradient End Color</label>
        <input type="color" name="gradient_end" value="{{ old('gradient_end', $item->gradient_end) }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Bullet Points (one per line)</label>
        <textarea name="items" rows="4">{{ old('items', $item->items) }}</textarea>
    </div>
    <div class="ka-field">
        <label>Link URL</label>
        <input name="link_url" value="{{ old('link_url', $item->link_url) }}">
    </div>
    <div class="ka-field">
        <label>Link Label</label>
        <input name="link_label" value="{{ old('link_label', $item->link_label) }}">
    </div>
    <label class="ka-check"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Explore Card</button>
    <a class="ka-btn ka-btn-light" href="/admin/explore-items">Cancel</a>
</div>
</form>
</section>
@endsection
