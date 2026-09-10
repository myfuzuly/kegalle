@extends('layouts.admin')
@section('title','Reports')
@section('page','Reports')
@section('heading','Listing Reports')
@section('subheading','User-submitted reports requiring moderation')

@section('content')
@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

<div class="flex-g8-mb20-fw">
    @foreach(['pending','reviewed','actioned','dismissed','all'] as $s)
    <a href="?status={{ $s }}" class="report-filter-tab {{ $status===$s ? 'report-filter-tab--active' : '' }}">{{ ucfirst($s) }}</a>
    @endforeach
</div>

@if($reports->isEmpty())
<div class="center-48b-muted">No reports found.</div>
@else
<div class="overflow-x-auto">
<table class="table-compact">
<thead>
<tr class="thead-bg">
    <th class="th-std">Listing</th>
    <th class="th-std">Reporter</th>
    <th class="th-std">Reason</th>
    <th class="th-std">Details</th>
    <th class="th-std">Status</th>
    <th class="th-std">Date</th>
    <th class="th-std">Actions</th>
</tr>
</thead>
<tbody>
@foreach($reports as $report)
<tr class="{{ $loop->even ? 'tr-even' : '' }}">
    <td class="td-std">
        @if($report->listing)
        <a class="link-primary" href="/listings/{{ $report->listing->slug }}" target="_blank">{{ Str::limit($report->listing->title, 40) }}</a>
        @else<span class="text-muted">Deleted</span>@endif
    </td>
    <td class="td-std">{{ $report->user->name ?? '—' }}<br><span class="text-hint-xs">{{ $report->user->email ?? '' }}</span></td>
    <td class="th-cell">{{ $report->reason }}</td>
    <td class="p10-12-mw220">{{ $report->details ?: '—' }}</td>
    <td class="td-std">
        <span class="report-status-badge report-status-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
    </td>
    <td class="td-muted">{{ $report->created_at->diffForHumans() }}</td>
    <td class="td-std">
        @if($report->status === 'pending')
        <form method="POST" action="/admin/reports/{{ $report->id }}" class="d-inline">@csrf @method('PATCH')
            <input type="hidden" name="status" value="reviewed">
            <button class="pill-blue-btn" type="submit">Review</button>
        </form>
        <form method="POST" action="/admin/reports/{{ $report->id }}" class="d-inline">@csrf @method('PATCH')
            <input type="hidden" name="status" value="actioned">
            <button class="pill-red-btn" type="submit" data-confirm="Remove listing?">Remove Ad</button>
        </form>
        <form method="POST" action="/admin/reports/{{ $report->id }}" class="d-inline">@csrf @method('PATCH')
            <input type="hidden" name="status" value="dismissed">
            <button class="pill-gray-btn" type="submit">Dismiss</button>
        </form>
        @else
        <span class="text-hint-xs">by {{ $report->reviewer->name ?? '—' }}<br>{{ $report->reviewed_at?->diffForHumans() }}</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="mt-20">{{ $reports->appends(['status'=>$status])->links() }}</div>
@endif
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Delegated confirm handler
document.addEventListener('click',function(e){
    var btn=e.target.closest('[data-confirm]');
    if(!btn) return;
    if(!confirm(btn.dataset.confirm)) e.preventDefault();
});
</script>
@endpush
