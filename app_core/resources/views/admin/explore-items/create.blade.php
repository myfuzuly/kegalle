@extends('layouts.admin')
@section('title','Add Explore Card')
@section('page','Explore Kegalle')
@section('heading','Add Explore Card')
@section('subheading','Create a new card for the homepage "Explore in Kegalle" section')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/explore-items">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/explore-items" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Card Photo (optional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 3MB. If left empty, the icon + gradient below is used instead.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Title</label>
        <input name="title" value="{{ old('title') }}" required placeholder="e.g. Activities, Tourist Places">
    </div>
    <div class="ka-field">
        <label>Icon (emoji, used when no photo is set)</label>
        <input name="icon" value="{{ old('icon', '📍') }}" maxlength="10">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
    </div>
    <div class="ka-field">
        <label>Gradient Start Color</label>
        <input type="color" name="gradient_start" value="{{ old('gradient_start', '#1B5E20') }}">
    </div>
    <div class="ka-field">
        <label>Gradient End Color</label>
        <input type="color" name="gradient_end" value="{{ old('gradient_end', '#388E3C') }}">
    </div>
    <div class="ka-field ka-span-2">
        <label>Bullet Points (one per line)</label>
        <textarea name="items" rows="4" placeholder="Hiking &amp; Trekking&#10;Water Activities&#10;Camping &amp; Outdoor">{{ old('items') }}</textarea>
    </div>
    <div class="ka-field">
        <label>Link URL</label>
        <input name="link_url" value="{{ old('link_url') }}" placeholder="/listings?categories[]=activities">
    </div>
    <div class="ka-field">
        <label>Link Label</label>
        <input name="link_label" value="{{ old('link_label') }}" placeholder="Explore Activities →">
    </div>
    <label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Create Explore Card</button>
    <a class="ka-btn ka-btn-light" href="/admin/explore-items">Cancel</a>
</div>
</form>
</section>
@endsection
