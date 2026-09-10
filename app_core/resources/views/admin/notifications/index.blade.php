@extends('layouts.admin')
@section('title','Notification Center')
@section('page','Notification Center')
@section('eyebrow','Overview')
@section('page_heading','Notification Center')

@section('actions')
<form method="post" action="/admin/notifications/read-all">
    @csrf
    <button class="ka-btn ka-btn-light fs12-p14-flex">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Mark all read
    </button>
</form>
@endsection

@push('styles')

@endpush

@section('content')
<section class="sa-card ovh-r12">

    {{-- Stats --}}
    <div class="kn-stats">
        <div>
            <span class="kn-stat-num">{{ $notifications->total() }}</span>
            <span class="kn-stat-label">Total</span>
        </div>
        @if($unreadCount)
        <div class="kn-stat-div"></div>
        <span class="kn-unread-pill"><span class="kn-unread-dot"></span>{{ $unreadCount }} Unread</span>
        @endif
    </div>

    {{-- One-line filter — native selects only, Select2 nuked via JS --}}
    <div class="kn-filterbar">
        <form method="get">
            <select name="type" class="kn-sel no-select2">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(request('type')===$type)>{{ str_replace('_',' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
            <select name="status" class="kn-sel no-select2">
                <option value="">All Status</option>
                <option value="unread" @selected(request('status')==='unread')>Unread</option>
                <option value="read"   @selected(request('status')==='read')>Read</option>
            </select>
            <span class="kn-filterdiv"></span>
            <button type="submit" class="kn-btnfilter">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="16" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="16" y2="18"/></svg>
                Filter
            </button>
            @if(request()->hasAny(['type','status']))
            <a href="/admin/notifications" class="kn-btnclear">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear
            </a>
            <div class="kn-filter-tags">
                @if(request('type'))<span class="kn-filter-tag">Type: {{ str_replace('_',' ', ucfirst(request('type'))) }}</span>@endif
                @if(request('status'))<span class="kn-filter-tag">Status: {{ ucfirst(request('status')) }}</span>@endif
            </div>
            @endif
        </form>
    </div>

    {{-- Headers --}}
    <div class="kn-thead">
        <span>Type</span>
        <span>Notification</span>
        <span>When</span>
        <span>Status</span>
        <span>Action</span>
    </div>

    {{-- Rows --}}
    <div class="kn-list">
    @forelse($notifications as $n)
        @php
            $rawType = strtolower(str_replace([' ','-'],'_', $n->type ?? ''));
            $badgeClass = in_array($rawType,['new_user','new_listing','new_classified','new_store','system'])
                ? 't-'.$rawType : 't-default';
        @endphp
        <div class="kn-row {{ $n->is_read ? '' : 'is-unread' }}">
            <div><span class="kn-badge {{ $badgeClass }}">{{ str_replace('_',' ', ucfirst($n->type)) }}</span></div>
            <div>
                <div class="kn-title">{{ $n->title }}</div>
                @if($n->message)<div class="kn-msg">{{ $n->message }}</div>@endif
            </div>
            <div class="kn-when">{{ $n->created_at?->diffForHumans() }}</div>
            <div>
                @if($n->is_read)
                    <span class="kn-s-read">Read</span>
                @else
                    <span class="kn-s-unread">Unread</span>
                @endif
            </div>
            <div>
                @if($n->link)
                    <form method="post" action="/admin/notifications/{{ $n->id }}/read" class="d-inline">@csrf
                        <button class="kn-act-btn">View →</button>
                    </form>
                @elseif(!$n->is_read)
                    <form method="post" action="/admin/notifications/{{ $n->id }}/read" class="d-inline">@csrf
                        <button class="kn-act-ghost">Mark read</button>
                    </form>
                @else
                    <span class="color-d0-fs12">—</span>
                @endif
            </div>
        </div>
    @empty
        <div class="kn-empty">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span>{{ request()->hasAny(['type','status']) ? 'No notifications match your filters.' : 'No notifications yet.' }}</span>
            @if(request()->hasAny(['type','status']))<a href="/admin/notifications" class="kn-btnclear mt-4px">Clear filters</a>@endif
        </div>
    @endforelse
    </div>

    <div class="kn-pager">{{ $notifications->links('vendor.pagination.ka-admin') }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
/* Force-destroy Select2 on notification filter selects, regardless of other init scripts */
(function killSelect2OnFilters() {
    function destroy() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;
        jQuery('.kn-filterbar select').each(function () {
            try { jQuery(this).select2('destroy'); } catch (e) {}
            jQuery(this).show().css({ display: '', width: '', visibility: '' });
        });
    }
    /* Run after all DOMContentLoaded handlers have fired */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(destroy, 50); });
    } else {
        setTimeout(destroy, 50);
    }
})();
</script>
@endpush
