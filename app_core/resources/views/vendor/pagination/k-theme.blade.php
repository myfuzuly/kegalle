@if ($paginator->hasPages())
    <nav class="k-pagination" role="navigation" aria-label="Pagination Navigation">
        @if ($paginator->onFirstPage())
            <span class="k-page-btn wide" style="opacity:.4;cursor:default">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="k-page-btn wide">← Prev</a>
        @endif

        @php
            $window = \Illuminate\Pagination\UrlWindow::make($paginator);
        @endphp

        @foreach ($window['first'] ?? [] as $page => $url)
            <a href="{{ $url }}" class="k-page-btn {{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if (! empty($window['slider']) && ($window['first'] ?? null))
            <span class="k-page-btn" style="border:none;background:none">…</span>
        @endif

        @foreach ($window['slider'] ?? [] as $page => $url)
            <a href="{{ $url }}" class="k-page-btn {{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if (! empty($window['last']))
            <span class="k-page-btn" style="border:none;background:none">…</span>
        @endif

        @foreach ($window['last'] ?? [] as $page => $url)
            <a href="{{ $url }}" class="k-page-btn {{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="k-page-btn wide">Next →</a>
        @else
            <span class="k-page-btn wide" style="opacity:.4;cursor:default">Next →</span>
        @endif
    </nav>
@endif
