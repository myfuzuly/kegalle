@extends('layouts.admin')
@section('title','Edit Listing')
@section('page','Products / Ads')
@section('heading','Edit Listing')
@section('subheading','Create or update a listing, assign user, category, location and status')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/listings">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/listings/{{ $listing->id }}">
@csrf
@method('PUT')

<div class="ka-form-grid">
    <div class="ka-field"><label>Post As User</label><select name="user_id" required><option value="">Select User</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('user_id', $listing->user_id ?? '') == $user->id)>{{ $user->name }} — {{ $user->email }}</option>@endforeach</select></div>
    <div class="ka-field"><label>Store</label><select name="store_id"><option value="">No Store</option>@foreach($stores as $store)<option value="{{ $store->id }}" @selected(old('store_id', $listing->store_id ?? '') == $store->id)>{{ $store->name }}</option>@endforeach</select></div>
    <div class="ka-field ka-span-2"><label>Listing Title</label><input name="title" value="{{ old('title', $listing->title ?? '') }}" required></div>
    <div class="ka-field ka-span-2"><label>Slug URL</label><input name="slug" value="{{ old('slug', $listing->slug ?? '') }}" placeholder="auto-generated-if-empty"><small>Used for public listing URL.</small></div>
    <div class="ka-field"><label>Category / Sub Category</label><select name="category_id"><option value="">No Category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $listing->category_id ?? '') == $category->id)>{{ $category->parent ? $category->parent->name.' / ' : '' }}{{ $category->name }}</option>@endforeach</select></div>
    <div class="ka-field"><label>Location</label><select name="location_id"><option value="">No Location</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('location_id', $listing->location_id ?? '') == $location->id)>{{ $location->parent ? $location->parent->name.' / ' : '' }}{{ $location->name }} ({{ ucfirst($location->type) }})</option>@endforeach</select></div>
    <div class="ka-field"><label>Manual Location Text</label><input name="location" value="{{ old('location', $listing->location ?? '') }}"></div>
    <div class="ka-field"><label>Price</label><input name="price" type="number" step="0.01" value="{{ old('price', $listing->price ?? '') }}"></div>
    <div class="ka-field"><label>Type</label><select name="type" required><option value="product" @selected(old('type', $listing->type ?? 'product') === 'product')>Product</option><option value="classified" @selected(old('type', $listing->type ?? '') === 'classified')>Classified</option><option value="buy" @selected(old('type', $listing->type ?? '') === 'buy')>Buy</option><option value="sell" @selected(old('type', $listing->type ?? '') === 'sell')>Sell</option><option value="exchange" @selected(old('type', $listing->type ?? '') === 'exchange')>Exchange</option><option value="job" @selected(old('type', $listing->type ?? '') === 'job')>Job</option><option value="to-let" @selected(old('type', $listing->type ?? '') === 'to-let')>To-Let</option></select></div>
    <div class="ka-field"><label>Status</label><select name="status" required><option value="pending" @selected(old('status', $listing->status ?? '') === 'pending')>Pending</option><option value="approved" @selected(old('status', $listing->status ?? 'approved') === 'approved')>Approved</option><option value="rejected" @selected(old('status', $listing->status ?? '') === 'rejected')>Rejected</option><option value="suspended" @selected(old('status', $listing->status ?? '') === 'suspended')>Suspended</option></select></div>
    <div class="ka-field ka-span-2"><label>Description</label><textarea name="description">{{ old('description', $listing->description ?? '') }}</textarea></div>
    <label class="ka-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $listing->is_featured ?? false))> Featured Ad</label>
    <label class="ka-check"><input type="checkbox" name="is_top" value="1" @checked(old('is_top', $listing->is_top ?? false))> Top Ad</label>
</div>
<div class="ka-form-actions"><button class="ka-btn ka-btn-primary">Edit Listing</button><a href="/admin/listings" class="ka-btn ka-btn-light">Cancel</a></div>
</form>
</section>
@endsection
