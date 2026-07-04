@extends('layouts.admin')
@section('title','Add Brand')
@section('page','Brands')
@section('heading','Add New Brand')
@section('subheading','Create a new brand')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/brands">Back to Brands</a>@endsection

@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/brands">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Brand Name</label>
        <input name="name" value="{{ old('name') }}" required placeholder="e.g. Toyota, Samsung, Apple">
        @error('name')<small style="color:#d32f2f">{{ $message }}</small>@enderror
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}">
        <small>Lower numbers appear first.</small>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Create Brand</button>
    <a href="/admin/brands" class="ka-btn ka-btn-light">Cancel</a>
</div>
</form>
</section>
@endsection
