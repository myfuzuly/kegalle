@forelse($listings as $listing)
@php
    $st = $listing->status ?? 'pending';
    $pillMap = ['approved'=>'lp-pill-approved','active'=>'lp-pill-active','pending'=>'lp-pill-pending','rejected'=>'lp-pill-rejected','sold'=>'lp-pill-sold','draft'=>'lp-pill-draft'];
    $pillCls = $pillMap[$st] ?? 'lp-pill-draft';
    $pillLabel = $st==='approved' ? 'Live' : ucfirst($st);
    $expHtml = '';
    if($listing->expires_at) {
        $exp = $listing->expires_at;
        if($exp->isPast()) $expHtml = '<span class="lp-exp lp-exp-dead">⚠ Expired</span>';
        elseif($exp->diffInDays() < 3) $expHtml = '<span class="lp-exp lp-exp-warn">⏳ Expires '.$exp->diffForHumans().'</span>';
        else $expHtml = '<span class="lp-exp lp-exp-ok">Expires '.$exp->diffForHumans().'</span>';
    }
    $firstImage = $listing->images->first();
    $thumb = $firstImage ? asset('storage/' . $firstImage->path) : null;
@endphp
<div class="lp-row" data-title="{{ strtolower($listing->title) }}">
    <div class="lp-thumb">
        @if($thumb)<img src="{{ $thumb }}" alt="{{ $listing->title }}" loading="lazy">@else📦@endif
    </div>
    <div class="lp-info">
        <div class="lp-title">{{ $listing->title }}</div>
        <div class="lp-meta">
            @if($listing->category)<span class="lp-cat">{{ $listing->category->name }}</span>@endif
            <span class="lp-time">{{ $listing->created_at?->diffForHumans() }}</span>
            {!! $expHtml !!}
        </div>
    </div>
    <div class="lp-price-col">
        <div class="lp-price">LKR {{ number_format($listing->price ?? 0) }}</div>
        <div class="lp-price-sub">{{ ucfirst($listing->type ?? 'listing') }}</div>
    </div>
    @php
        $stock = $listing->stock;
        $stockCls = is_null($stock) ? 'lp-stock-na' : ($stock === 0 ? 'lp-stock-out' : ($stock <= 5 ? 'lp-stock-low' : 'lp-stock-ok'));
        $stockLabel = is_null($stock) ? '—' : ($stock === 0 ? 'Out' : $stock);
    @endphp
    <div class="lp-stock-cell" title="Click to edit stock">
        <span class="lp-stock-badge {{ $stockCls }}" data-id="{{ $listing->id }}" data-stock="{{ $stock ?? '' }}" data-csrf="{{ csrf_token() }}">{{ $stockLabel }}</span>
        <input class="lp-stock-input" type="number" min="0" max="99999" step="1" style="display:none" data-id="{{ $listing->id }}" data-csrf="{{ csrf_token() }}">
    </div>
    <span class="lp-pill {{ $pillCls }}">{{ $pillLabel }}</span>
    <div class="lp-actions">
        <a href="/listings/{{ $listing->slug }}" class="lp-btn lp-btn-view" target="_blank">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            View
        </a>
        <a href="/dashboard/listings/{{ $listing->id }}/edit" class="lp-btn lp-btn-edit">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        @if($st !== 'sold')
        <form method="POST" action="/dashboard/listings/{{ $listing->id }}/sold" data-confirm="Mark as sold?" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="lp-btn lp-btn-sold">✓ Sold</button>
        </form>
        @endif
        @if($listing->expires_at && ($listing->expires_at->isPast() || $listing->expires_at->diffInDays() < 7))
        <form method="POST" action="/dashboard/listings/{{ $listing->id }}/renew" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="lp-btn lp-btn-renew">&#x21BB; Renew</button>
        </form>
        @endif
        @if(in_array($st, ['approved','active','published']))
        <form method="POST" action="/dashboard/listings/{{ $listing->id }}/bump" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="lp-btn lp-btn-boost" title="Boost to top (once per 24h)">&#x1F680; Boost</button>
        </form>
        @endif
        <form method="POST" action="/dashboard/listings/{{ $listing->id }}" data-confirm="Delete this listing permanently?" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="lp-btn lp-btn-del">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" d="M19 6l-1 14H6L5 6"/></svg>
            </button>
        </form>
    </div>
</div>
@empty
<div class="lp-empty">
    <div class="lp-empty-icon">📋</div>
    <strong>No listings found</strong>
    <p>Try a different search or status filter.</p>
    <a href="/dashboard/listings/create" class="lp-empty-btn">+ Post New Listing</a>
</div>
@endforelse
