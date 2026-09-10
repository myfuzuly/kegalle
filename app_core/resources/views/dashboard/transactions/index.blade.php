@extends('layouts.dashboard')
@section('title','Transaction History')
@section('heading','Transaction History')
@section('subheading','Your offers, deals, and membership payments.')

@section('content')

@if($offers->isEmpty() && $payments->isEmpty())
<div class="center-64-muted">
    <div class="fs48-mb16">📋</div>
    <strong class="block-fs16-slate">No transactions yet</strong>
    <p class="fs-14">Accepted offers and membership payments will appear here.</p>
</div>
@endif

@if($offers->isNotEmpty())
<div class="kdl-section-title heading-15">Offer History</div>
<div class="ovx-auto-mb32">
<table class="table-compact">
<thead>
<tr class="thead-bg">
    <th class="th-std">Listing</th>
    <th class="th-std">Role</th>
    <th class="th-std">Offer Price</th>
    <th class="th-std">Status</th>
    <th class="th-std">Date</th>
</tr>
</thead>
<tbody>
@foreach($offers as $offer)
@php $sc=['accepted'=>'#16a34a','rejected'=>'#ef4444','completed'=>'#3b82f6']; @endphp
<tr style="border-bottom:1px solid #f1f5f9;{{ $loop->even?'background:#fafafa':'' }}">
    <td class="td-std">
        @if($offer->listing)
        <a class="link-primary" href="/listings/{{ $offer->listing->slug }}">{{ Str::limit($offer->listing->title,45) }}</a>
        @else<span class="text-muted">Deleted listing</span>@endif
    </td>
    <td class="td-std">
        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $offer->role==='buyer'?'#eff6ff':'#f0fdf4' }};color:{{ $offer->role==='buyer'?'#3b82f6':'#16a34a' }}">{{ ucfirst($offer->role) }}</span>
    </td>
    <td class="th-cell">LKR {{ number_format($offer->offered_price) }}</td>
    <td class="td-std">
        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ ($sc[$offer->status]??'#94a3b8') }}20;color:{{ $sc[$offer->status]??'#94a3b8' }}">{{ ucfirst($offer->status) }}</span>
    </td>
    <td class="td-muted">{{ $offer->updated_at->diffForHumans() }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif

@if($payments->isNotEmpty())
<div class="kdl-section-title heading-15">Membership Payments</div>
<div class="overflow-x-auto">
<table class="table-compact">
<thead>
<tr class="thead-bg">
    <th class="th-std">Plan</th>
    <th class="th-std">Amount</th>
    <th class="th-std">Status</th>
    <th class="th-std">Reference</th>
    <th class="th-std">Date</th>
</tr>
</thead>
<tbody>
@foreach($payments as $pay)
@php $pc=['paid'=>'#16a34a','pending'=>'#f59e0b','failed'=>'#ef4444','refunded'=>'#3b82f6']; @endphp
<tr style="border-bottom:1px solid #f1f5f9;{{ $loop->even?'background:#fafafa':'' }}">
    <td class="th-cell">{{ optional($pay->membershipPlan)->name ?? ($pay->meta['plan_slug'] ?? '—') }}</td>
    <td class="th-cell">LKR {{ number_format($pay->amount ?? 0) }}</td>
    <td class="td-std">
        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ ($pc[$pay->status]??'#94a3b8') }}20;color:{{ $pc[$pay->status]??'#94a3b8' }}">{{ ucfirst($pay->status) }}</span>
    </td>
    <td class="p10-12-mono">{{ $pay->reference ?? '—' }}</td>
    <td class="td-muted">{{ $pay->created_at->diffForHumans() }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif

@endsection
