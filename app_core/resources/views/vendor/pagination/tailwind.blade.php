@if ($paginator->hasPages())
    <nav class="k-pagination" role="navigation" aria-label="Pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="k-page-btn prev" aria-disabled="true" style="opacity:.5;cursor:default">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="k-page-btn prev">← Prev</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="k-page-btn" style="cursor:default">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="k-page-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="k-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="k-page-btn next">Next →</a>
        @else
            <span class="k-page-btn next" aria-disabled="true" style="opacity:.5;cursor:default">Next →</span>
        @endif
    </nav>
@endif
