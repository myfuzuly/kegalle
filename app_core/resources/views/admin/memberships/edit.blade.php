@extends('layouts.admin')

@section('title','Edit Membership Plan')
@section('page','Memberships')
@section('heading','Edit Membership Plan')
@section('subheading','Update pricing, limits and visibility')

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/memberships">Back</a>
@endsection

@section('content')
<section class="sa-card">
    <form class="ka-premium-form" method="post" action="/admin/memberships/{{ $plan->id }}">
        @csrf
        @method('PUT')

        <div class="ka-form-grid">
            <div class="ka-field">
                <label>Plan Name</label>
                <input name="name" value="{{ old('name', $plan->name) }}" required>
            </div>
            <div class="ka-field">
                <label>Slug</label>
                <input name="slug" value="{{ old('slug', $plan->slug) }}" required>
            </div>
            <div class="ka-field">
                <label>Price (LKR)</label>
                <input name="price" type="number" step="0.01" value="{{ old('price', $plan->price) }}" required>
            </div>
            <div class="ka-field">
                <label>Duration (days)</label>
                <input name="duration_days" type="number" value="{{ old('duration_days', $plan->duration_days) }}" required>
            </div>
            <div class="ka-field">
                <label>Ad Limit</label>
                <input name="ad_limit" type="number" value="{{ old('ad_limit', $plan->ad_limit) }}" required>
            </div>
            <div class="ka-field">
                <label>Product Limit</label>
                <input name="product_limit" type="number" value="{{ old('product_limit', $plan->product_limit) }}" required>
            </div>
            <div class="ka-field">
                <label>Store Limit</label>
                <input name="store_limit" type="number" value="{{ old('store_limit', $plan->store_limit) }}">
            </div>
            <div class="ka-field">
                <label>Featured Quota</label>
                <input name="featured_quota" type="number" value="{{ old('featured_quota', $plan->featured_quota) }}">
            </div>

            <label class="ka-check">
                <input type="checkbox" name="is_active" value="1" @checked($plan->is_active)>
                Active
            </label>
        </div>

        <div class="ka-form-actions">
            <button class="ka-btn ka-btn-primary">Update Plan</button>
            <a class="ka-btn ka-btn-light" href="/admin/memberships">Cancel</a>
        </div>
    </form>
</section>
@endsection
