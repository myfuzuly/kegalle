@extends('layouts.admin')
@section('title','Memberships')
@section('page','Memberships')
@section('heading','Membership Plans')
@section('subheading','Create and manage paid membership tiers for sellers')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Add Plan</h2><span>Full CRUD</span></div>
<form class="ka-premium-form" method="post" action="/admin/memberships">
@csrf
<div class="ka-form-grid">
    <div class="ka-field"><label>Plan Name</label><input name="name" placeholder="e.g. Pro Seller" required></div>
    <div class="ka-field"><label>Price (LKR)</label><input name="price" type="number" step="0.01" placeholder="0.00" required></div>
    <div class="ka-field"><label>Duration (days)</label><input name="duration_days" type="number" placeholder="30" required></div>
    <div class="ka-field"><label>Ad Limit</label><input name="ad_limit" type="number" placeholder="10" required></div>
    <div class="ka-field"><label>Product Limit</label><input name="product_limit" type="number" placeholder="10" required></div>
    <div class="ka-field"><label>Store Limit</label><input name="store_limit" type="number" placeholder="1 (default)"></div>
    <div class="ka-field"><label>Featured Quota</label><input name="featured_quota" type="number" placeholder="0 (default)"></div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Add Plan</button>
</div>
</form>
</section>
<section class="sa-card"><div class="sa-card-head"><h2>Plans from Database</h2><span>{{ $plans->count() }} plans</span></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-memberships"><thead><tr><th>Plan</th><th>Price</th><th>Duration</th><th>Ad / Product Limit</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($plans as $plan)<tr><td><b>{{ $plan->name }}</b><small>#{{ $plan->id }}</small></td><td>LKR {{ number_format($plan->price, 2) }}</td><td>{{ $plan->duration_days }} days</td><td>{{ $plan->ad_limit }} / {{ $plan->product_limit }}</td><td><span class="sa-status {{ $plan->is_active ? 'active' : 'suspended' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span></td><td class="sa-actions-inline"><a href="/admin/memberships/{{ $plan->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/memberships/{{ $plan->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form><form method="post" action="/admin/memberships/{{ $plan->id }}" onsubmit="return confirm('Delete this plan?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form></td></tr>@empty<tr><td colspan="6">No membership plans found.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
