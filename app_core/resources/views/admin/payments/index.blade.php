@extends('layouts.admin')
@section('title','Payments')
@section('page','Payments')
@section('eyebrow','Finance')
@section('page_heading','Payment Ledger')
@section('subheading','Review and update the status of membership and store payments')

@section('content')

@if(session('success'))
<div class="prf-alert prf-alert-success" class="mb-20">{{ session('success') }}</div>
@endif

<section class="sa-card">
<div class="sa-card-head">
    <h2>All Payments</h2>
    <span>{{ $payments->total() }} records</span>
</div>
<div class="sa-table-wrap">
<table class="sa-table sa-table-payments">
<thead><tr>
    <th>User</th>
    <th>Plan</th>
    <th>Amount</th>
    <th>Gateway</th>
    <th>Reference</th>
    <th>Status</th>
    <th>Slip</th>
    <th>Date</th>
    <th>Actions</th>
</tr></thead>
<tbody>
@forelse($payments as $payment)
<tr>
    <td>
        <div class="kaa-name-cell">
            <span class="kaa-name-primary">{{ $payment->user->name ?? 'Unknown' }}</span>
            <span class="kaa-name-sub">#{{ $payment->id }}</span>
        </div>
    </td>
    <td class="fs-13">{{ $payment->membershipPlan->name ?? '—' }}</td>
    <td class="fs13-fw7-tnum">{{ $payment->currency ?? 'LKR' }} {{ number_format($payment->amount, 2) }}</td>
    <td><span class="kaa-badge kaa-badge-gray">{{ ucfirst($payment->gateway ?? 'offline') }}</span></td>
    <td class="fs12-muted-tnum">{{ $payment->reference ?? '—' }}</td>
    <td>
        <span class="sa-status {{ $payment->status === 'paid' ? 'active' : ($payment->status === 'failed' ? 'suspended' : ($payment->status === 'pending' ? 'pending' : '')) }}">
            {{ ucfirst($payment->status ?? 'pending') }}
        </span>
    </td>
    <td>
        @php $slip = $payment->meta['slip'] ?? null; @endphp
        @if($slip)
        <a class="link-green-sm" href="{{ route('admin.payments.slip', $payment) }}" target="_blank">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          View Slip
        </a>
        @else
        <span class="fs12-muted-var">No slip</span>
        @endif
    </td>
    <td class="fs12-muted-nw">{{ $payment->created_at?->format('M d, Y') }}</td>
    <td>
        <div class="kaa-actions">
            @if($payment->status === 'pending')
            <form method="post" action="/admin/payments/{{ $payment->id }}/approve" class="d-contents">
                @csrf
                <button class="kaa-act highlight-green" data-confirm="Approve this payment and activate the membership?">
                    ✓ Approve
                </button>
            </form>
            <form method="post" action="/admin/payments/{{ $payment->id }}/reject" class="d-contents">
                @csrf
                <button class="kaa-act kaa-act-del" data-confirm="Reject this payment?">Reject</button>
            </form>
            @else
            <form method="post" action="/admin/payments/{{ $payment->id }}/status" class="d-contents">
                @csrf
                <select class="select-32h" name="status" onchange="this.form.submit()">
                    <option value="pending"  @selected($payment->status==='pending')>Pending</option>
                    <option value="paid"     @selected($payment->status==='paid')>Paid</option>
                    <option value="failed"   @selected($payment->status==='failed')>Failed</option>
                    <option value="refunded" @selected($payment->status==='refunded')>Refunded</option>
                </select>
            </form>
            @endif
            <form method="post" action="/admin/payments/{{ $payment->id }}" data-confirm="Delete this payment record?" class="d-contents">
                @csrf @method('DELETE')
                <button class="kaa-act kaa-act-del">Delete</button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="9">
    <div class="kaa-empty">
        <span class="kaa-empty-icon">💳</span>
        <span class="kaa-empty-text">No payment records found</span>
    </div>
</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="kaa-pagination">{{ $payments->links() }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Handle data-confirm on buttons and forms
document.addEventListener('click', function(e) {
    var btn = e.target.closest('button[data-confirm]');
    if (btn && !confirm(btn.dataset.confirm)) e.preventDefault();
});
document.querySelectorAll('form[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm(form.dataset.confirm)) e.preventDefault();
    });
});
</script>
@endpush
