@extends('layouts.admin')
@section('title','Deal Management')
@section('page','Deals')
@section('heading','Deal Management')
@section('subheading','Approve, reject and manage seller deal submissions — flash deals, featured deals and promotions')
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>{{ $pendingCount }} pending approval</p><h1>🔥 Deal Management</h1></div>
<form class="sa-search" method="get"><input name="q" value="{{ request('q') }}" placeholder="Search deals"><select name="status"><option value="">All Status</option><option value="pending" @selected(request('status')==='pending')>Pending</option><option value="approved" @selected(request('status')==='approved')>Approved</option><option value="rejected" @selected(request('status')==='rejected')>Rejected</option><option value="expired" @selected(request('status')==='expired')>Expired</option></select><button>Filter</button></form></div>

@if(session('success'))<div style="background:#E8F5E9;color:#1B5E20;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>@endif

<div class="sa-table-wrap"><table class="sa-table sa-table-with-thumb"><thead><tr><th>Image</th><th>Listing</th><th>Seller / Store</th><th>Price</th><th>Discount</th><th>Period</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($deals as $deal)
@php
    $thumb = optional(optional($deal->listing)->images->first())->path ?? null;
    $statusYes = $deal->status === 'approved';
    $statusPending = $deal->status === 'pending';
    $markClass = $statusYes ? 'is-yes' : ($statusPending ? 'is-pending' : 'is-no');
    $markChar = $statusYes ? '✓' : ($statusPending ? '…' : '✕');
    $isActive = $deal->status === 'approved' && $deal->starts_at <= now() && $deal->ends_at >= now();
    $isExpired = $deal->ends_at < now();
@endphp
<tr>
    <td>@if($thumb)<img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="" style="width:40px;height:40px;border-radius:8px;object-fit:cover">@else<span style="width:40px;height:40px;border-radius:8px;background:#f5f7fb;display:flex;align-items:center;justify-content:center;font-size:16px">🛍️</span>@endif</td>
    <td><b>{{ \Illuminate\Support\Str::limit($deal->listing->title ?? 'Deleted', 25) }}</b><small>{{ optional(optional($deal->listing)->category)->name ?? '' }}</small></td>
    <td><small>{{ $deal->store->name ?? 'Personal' }}</small><br><small style="color:#888">{{ $deal->user->name ?? '' }}</small></td>
    <td><b style="color:#1B5E20">LKR {{ number_format($deal->deal_price) }}</b><br><small><s>{{ number_format($deal->original_price) }}</s></small></td>
    <td style="color:#D32F2F;font-weight:700">-{{ number_format($deal->discount_percent,0) }}%</td>
    <td><small>{{ $deal->starts_at->format('M d') }} — {{ $deal->ends_at->format('M d') }}</small>
        @if($isExpired)<br><span style="color:#D32F2F;font-size:10px">Expired</span>@elseif($isActive)<br><span style="color:#1B5E20;font-size:10px">● Live</span>@endif</td>
    <td>
        @if($deal->is_flash)<span style="background:#F9A825;color:#fff;font-size:10px;padding:1px 6px;border-radius:4px">⚡ Flash</span>@endif
        @if($deal->is_featured)<span style="background:#1B5E20;color:#fff;font-size:10px;padding:1px 6px;border-radius:4px">★ Featured</span>@endif
        @if(!$deal->is_flash && !$deal->is_featured)<small style="color:#888">Standard</small>@endif
    </td>
    <td><span class="sa-status-mark {{ $markClass }}">{{ $markChar }}</span></td>
    <td class="sa-actions-inline">
        @if($deal->listing)<a href="/listings/{{ $deal->listing->slug }}" target="_blank">View</a>@endif
        @if($deal->status === 'pending')
            <form method="post" action="/admin/deals/{{ $deal->id }}/approve">@csrf<button title="Approve">Approve</button></form>
            <form method="post" action="/admin/deals/{{ $deal->id }}/reject">@csrf<button class="danger" title="Reject">Reject</button></form>
        @endif
        <form method="post" action="/admin/deals/{{ $deal->id }}/feature">@csrf<button title="Toggle Featured">{{ $deal->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <form method="post" action="/admin/deals/{{ $deal->id }}/flash">@csrf<button title="Toggle Flash">{{ $deal->is_flash ? 'Unflash' : '⚡ Flash' }}</button></form>
        <form method="post" action="/admin/deals/{{ $deal->id }}" style="display:inline">@csrf @method('DELETE')<button class="danger" title="Delete">Del</button></form>
    </td>
</tr>
@empty <tr><td colspan="9">No deals found.</td></tr>@endforelse
</tbody></table></div>{{ $deals->links() }}</section>
@endsection
