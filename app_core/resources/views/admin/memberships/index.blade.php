@extends('layouts.admin')
@section('title','Memberships')
@section('page','Memberships')
@section('heading','Membership Plans')
@section('subheading','Create and manage paid membership tiers for sellers')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Add Plan</h2><span>Full CRUD</span></div>
<form class="sa-form ka-category-form" method="post" action="/admin/memberships" style="flex-wrap:wrap">@csrf
<input name="name" placeholder="Plan name" required>
<input name="price" type="number" step="0.01" placeholder="Price (LKR)" required>
<input name="duration_days" type="number" placeholder="Duration (days)" required>
<input name="ad_limit" type="number" placeholder="Ad limit" required>
<input name="product_limit" type="number" placeholder="Product limit" required>
<input name="store_limit" type="number" placeholder="Store limit (default 1)">
<input name="featured_quota" type="number" placeholder="Featured quota (default 0)">
<button>Add Plan</button>
</form>
</section>
<section class="sa-card"><div class="sa-card-head"><h2>Plans from Database</h2><span>{{ $plans->count() }} plans</span></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Plan</th><th>Price</th><th>Duration</th><th>Ad / Product Limit</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($plans as $plan)<tr><td><b>{{ $plan->name }}</b><small>#{{ $plan->id }}</small></td><td>LKR {{ number_format($plan->price, 2) }}</td><td>{{ $plan->duration_days }} days</td><td>{{ $plan->ad_limit }} / {{ $plan->product_limit }}</td><td><span class="sa-status {{ $plan->is_active ? 'active' : 'suspended' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span></td><td class="sa-actions-inline"><a href="/admin/memberships/{{ $plan->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/memberships/{{ $plan->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form><form method="post" action="/admin/memberships/{{ $plan->id }}" onsubmit="return confirm('Delete this plan?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form></td></tr>@empty<tr><td colspan="6">No membership plans found.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
