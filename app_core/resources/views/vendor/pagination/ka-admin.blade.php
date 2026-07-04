@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 20px;flex-wrap:wrap">
        <span style="font-size:12.5px;color:#667085">
            Showing <b>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</b> of <b>{{ $paginator->total() }}</b>
        </span>
        <div style="display:flex;gap:6px;align-items:center">
            @if ($paginator->onFirstPage())
                <span style="padding:7px 14px;border-radius:9px;border:1.5px solid #e5e8ef;font-size:12.5px;font-weight:600;color:#c3cad5;cursor:default">← Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding:7px 14px;border-radius:9px;border:1.5px solid #e5e8ef;font-size:12.5px;font-weight:600;color:#344054;text-decoration:none;background:#fff">← Prev</a>
            @endif

            @php $window = \Illuminate\Pagination\UrlWindow::make($paginator); @endphp
            @php $segments = array_values(array_filter([$window['first'] ?? null, $window['slider'] ?? null, $window['last'] ?? null])); @endphp
            @foreach ($segments as $i => $segment)
                @if ($i > 0)
                    <span style="padding:7px 6px;color:#98a2b3;font-size:12.5px">…</span>
                @endif
                @foreach ($segment as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" style="padding:7px 13px;border-radius:9px;background:#1B5E20;color:#fff;font-size:12.5px;font-weight:700">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding:7px 13px;border-radius:9px;border:1.5px solid #e5e8ef;font-size:12.5px;font-weight:600;color:#344054;text-decoration:none;background:#fff">{{ $page }}</a>
                    @endif
                @endforeach
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding:7px 14px;border-radius:9px;border:1.5px solid #e5e8ef;font-size:12.5px;font-weight:600;color:#344054;text-decoration:none;background:#fff">Next →</a>
            @else
                <span style="padding:7px 14px;border-radius:9px;border:1.5px solid #e5e8ef;font-size:12.5px;font-weight:600;color:#c3cad5;cursor:default">Next →</span>
            @endif
        </div>
    </nav>
@endif
