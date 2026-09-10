@if ($paginator->hasPages())
<nav class="kdpg-nav" role="navigation" aria-label="Pagination">
    <div class="kdpg-info">
        Showing <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong>
    </div>
    <div class="kdpg-btns">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="kdpg-btn kdpg-nav-btn kdpg-disabled">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="kdpg-btn kdpg-nav-btn" rel="prev" aria-label="Previous">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
            </a>
        @endif

        @php $window = \Illuminate\Pagination\UrlWindow::make($paginator); @endphp

        @foreach ($window['first'] ?? [] as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="kdpg-btn kdpg-active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="kdpg-btn">{{ $page }}</a>
            @endif
        @endforeach

        @if (!empty($window['slider']) && ($window['first'] ?? null))
            <span class="kdpg-ellipsis">…</span>
        @endif

        @foreach ($window['slider'] ?? [] as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="kdpg-btn kdpg-active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="kdpg-btn">{{ $page }}</a>
            @endif
        @endforeach

        @if (!empty($window['last']))
            <span class="kdpg-ellipsis">…</span>
        @endif

        @foreach ($window['last'] ?? [] as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="kdpg-btn kdpg-active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="kdpg-btn">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="kdpg-btn kdpg-nav-btn" rel="next" aria-label="Next">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </a>
        @else
            <span class="kdpg-btn kdpg-nav-btn kdpg-disabled">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
            </span>
        @endif
    </div>
</nav>
@endif
