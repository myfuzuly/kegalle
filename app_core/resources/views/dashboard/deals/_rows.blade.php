@forelse($deals as $deal)
@php
    $st = $deal->status ?? 'pending';
    $pillCls = match($st) { 'approved' => 'di-pill-approved', 'rejected' => 'di-pill-rejected', default => 'di-pill-pending' };
    $pillLabel = $st === 'approved' ? 'Live' : ucfirst($st);
    $thumb = null;
    try {
        $img = $deal->listing?->images()?->orderBy('sort_order')->first();
        if($img) $thumb = asset('storage/'.$img->path);
    } catch(\Throwable) {}
@endphp
<div class="di-row">
    <div class="di-thumb">
        @if($thumb)<img src="{{ $thumb }}" alt="{{ $deal->title ?? 'Deal image' }}" loading="lazy">@else🔥@endif
    </div>
    <div class="di-info">
        <div class="di-title">{{ \Illuminate\Support\Str::limit($deal->listing->title ?? 'Deleted listing', 50) }}</div>
        <div class="di-meta">
            @if(optional($deal->listing)->category)<span class="di-cat">{{ $deal->listing->category->name }}</span>@endif
            @if($deal->is_flash)<span class="di-flash">⚡ Flash Deal</span>@endif
        </div>
    </div>
    <div class="di-price-col">
        <div class="di-deal-price">LKR {{ number_format($deal->deal_price) }}</div>
        <div class="di-orig-price">LKR {{ number_format($deal->original_price) }}</div>
    </div>
    <div class="di-discount">-{{ number_format($deal->discount_percent, 0) }}%</div>
    <div class="di-dates">
        <strong>{{ $deal->starts_at->format('M d') }} – {{ $deal->ends_at->format('M d') }}</strong>
        {{ $deal->ends_at->format('Y') }}
    </div>
    <span class="di-pill {{ $pillCls }}">{{ $pillLabel }}</span>
    <div class="di-actions">
        @if($deal->listing)
            <a href="/listings/{{ $deal->listing->slug }}" class="di-btn di-btn-view" target="_blank">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </a>
        @endif
        <a href="/dashboard/deals/{{ $deal->id }}/edit" class="di-btn di-btn-edit">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        @if($st !== 'approved')
            <form method="POST" action="/dashboard/deals/{{ $deal->id }}" data-confirm="Delete this deal?" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="di-btn di-btn-del">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" d="M19 6l-1 14H6L5 6"/></svg>
                </button>
            </form>
        @endif
    </div>
</div>
@if($st === 'rejected' && $deal->admin_note)
<div class="di-admin-note">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <div><strong>Admin note:</strong> {{ $deal->admin_note }}</div>
</div>
@endif
@empty
<div class="di-empty">
    <div class="di-empty-icon">🔥</div>
    <strong>No deals found</strong>
    <p>Try a different filter or submit a new deal.</p>
</div>
@endforelse
