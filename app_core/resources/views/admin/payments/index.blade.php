@extends('layouts.admin')
@section('title','Payments')
@section('page','Payments')
@section('heading','Payments')
@section('subheading','Review and update the status of membership and store payments')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Payments from Database</h2><span>{{ $payments->total() }} payments</span></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-payments"><thead><tr><th>User</th><th>Plan</th><th>Amount</th><th>Gateway</th><th>Reference</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@forelse($payments as $payment)<tr>
    <td><b>{{ $payment->user->name ?? 'Unknown' }}</b><small>#{{ $payment->id }}</small></td>
    <td>{{ $payment->membershipPlan->name ?? '—' }}</td>
    <td>{{ $payment->currency ?? 'LKR' }} {{ number_format($payment->amount, 2) }}</td>
    <td>{{ ucfirst($payment->gateway ?? 'offline') }}</td>
    <td>{{ $payment->reference ?? '—' }}</td>
    <td><span class="sa-status {{ $payment->status === 'paid' ? 'active' : ($payment->status === 'failed' ? 'suspended' : '') }}">{{ ucfirst($payment->status ?? 'pending') }}</span></td>
    <td>{{ $payment->created_at?->format('Y-m-d') }}</td>
    <td class="sa-actions-inline">
        <form method="post" action="/admin/payments/{{ $payment->id }}/status" style="display:inline-flex;gap:4px">
            @csrf
            <select name="status" onchange="this.form.submit()" style="font-size:12px">
                <option value="pending" @selected($payment->status==='pending')>Pending</option>
                <option value="paid" @selected($payment->status==='paid')>Paid</option>
                <option value="failed" @selected($payment->status==='failed')>Failed</option>
                <option value="refunded" @selected($payment->status==='refunded')>Refunded</option>
            </select>
        </form>
        <form method="post" action="/admin/payments/{{ $payment->id }}" onsubmit="return confirm('Delete this payment record?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>@empty<tr><td colspan="8">No payments found.</td></tr>@endforelse
</tbody></table></div>{{ $payments->links() }}</section>
@endsection
