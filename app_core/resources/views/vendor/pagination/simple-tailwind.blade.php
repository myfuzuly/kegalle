@if ($paginator->hasPages())
    <nav class="k-pagination" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="k-page-btn prev" aria-disabled="true" style="opacity:.5;cursor:default">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="k-page-btn prev">← Prev</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="k-page-btn next">Next →</a>
        @else
            <span class="k-page-btn next" aria-disabled="true" style="opacity:.5;cursor:default">Next →</span>
        @endif
    </nav>
@endif
