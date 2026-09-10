@extends('layouts.dashboard')
@section('banner_sub', 'Track your membership payments and billing history.')
@section('title','Payments')
@section('eyebrow','Account')
@section('heading','Payment History')
@section('subheading','Your membership upgrade requests and payment status.')

@section('content')

@if($payments->isEmpty())
<div class="pay-empty">
  <div class="pay-empty-icon">💳</div>
  <div class="pay-empty-title">No payments yet</div>
  <div class="pay-empty-sub">Upgrade your membership to unlock more listings and features.</div>
  <a href="/dashboard/membership" class="pay-empty-cta">View Plans →</a>
</div>
@else
<div class="pay-card">
  <div class="pay-card-head">
    <span class="pay-card-title">All Transactions</span>
    <span class="pay-card-count">{{ $payments->total() }} record{{ $payments->total() === 1 ? '' : 's' }}</span>
  </div>
  <div class="pay-table-wrap">
  <table class="pay-table">
    <thead>
      <tr>
        @foreach(['Plan','Amount','Reference','Status','Date','Slip'] as $h)
        <th>{{ $h }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
    @foreach($payments as $p)
    @php
      $planName = $p->membershipPlan->name ?? ($p->meta['plan_name'] ?? ucfirst($p->meta['plan_slug'] ?? 'Unknown'));
      $slipPath = $p->meta['slip'] ?? null;
      $statusCls = ['pending'=>'pay-status-pending','paid'=>'pay-status-paid','failed'=>'pay-status-failed','refunded'=>'pay-status-refunded'][$p->status] ?? 'pay-status-pending';
      $statusLabel = ['pending'=>'Pending Review','paid'=>'Approved ✓','failed'=>'Rejected','refunded'=>'Refunded'][$p->status] ?? 'Pending';
    @endphp
    <tr>
      <td>
        <div class="pay-plan-name">{{ $planName }}</div>
        <div class="pay-plan-sub">30 days · bank transfer</div>
      </td>
      <td class="pay-amount">LKR {{ number_format($p->amount, 2) }}</td>
      <td class="pay-ref">{{ $p->reference ?? '—' }}</td>
      <td><span class="pay-status {{ $statusCls }}">{{ $statusLabel }}</span></td>
      <td class="pay-date">{{ $p->created_at?->format('M d, Y') }}</td>
      <td>
        @if($slipPath)
        <a href="{{ Storage::url($slipPath) }}" target="_blank" class="pay-slip-link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          View
        </a>
        @else
        <span class="pay-no-slip">—</span>
        @endif
      </td>
    </tr>
    @endforeach
    </tbody>
  </table>
  </div>
  @if($payments->hasPages())
  <div class="pay-pagination">{{ $payments->links() }}</div>
  @endif
</div>
@endif

@endsection
