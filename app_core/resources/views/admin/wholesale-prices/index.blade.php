@extends('layouts.admin')
@section('title','Wholesale Prices')
@section('page','Wholesale Prices')
@section('heading','Wholesale Market Prices')
@section('subheading','Enter daily prices — auto-rolled forward to next day at 11:00 AM (Sri Lanka time)')

@section('content')
<section class="sa-card">

    {{-- Header row --}}
    <div class="flex-row justify-between ka-card-head-row" style="flex-wrap:wrap;gap:.75rem">
        <div>
            <h2 class="ka-section-title-row">Price Board</h2>
            <p class="ka-section-sub-row">
                Showing <strong>{{ count($prices) }}</strong> commodities for
                <strong>{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</strong>
            </p>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
            {{-- Date picker --}}
            <form method="GET" style="display:flex;gap:.5rem;align-items:center">
                <select name="date" class="ksd-select" onchange="this.form.submit()" style="font-size:.82rem">
                    @foreach($dates as $d)
                        <option value="{{ $d }}" @selected($d == $date)>{{ \Carbon\Carbon::parse($d)->format('d M Y') }}</option>
                    @endforeach
                </select>
            </form>
            <button type="button" id="wpAddRow" class="ka-btn ka-btn-secondary" style="font-size:.82rem">+ Add Row</button>
            @if(count($prices))
            <form method="POST" action="{{ route('admin.wholesale-prices.destroy', $date) }}" onsubmit="return confirm('Delete all prices for {{ $date }}?')">
                @csrf @method('DELETE')
                <button class="ka-btn" style="font-size:.82rem;background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2)">Delete Date</button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert-green-inner">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-red-inner">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif

    {{-- Price entry form --}}
    <form method="POST" action="{{ route('admin.wholesale-prices.store') }}" id="wpForm">
        @csrf
        <input type="hidden" name="date" id="wpDate" value="{{ $date }}">

        <div class="sa-table-wrap" style="margin-top:1rem">
            <table class="sa-table" id="wpTable">
                <thead><tr>
                    <th style="width:180px">Commodity</th>
                    <th style="width:140px">Category</th>
                    <th style="width:110px">Unit</th>
                    <th style="width:110px">Min (LKR)</th>
                    <th style="width:110px">Max (LKR)</th>
                    <th style="width:110px">Avg (LKR)</th>
                    <th style="width:44px"></th>
                </tr></thead>
                <tbody id="wpBody">
                @php
                $categories = \App\Models\WholesalePrice::categories();
                @endphp
                @forelse($prices as $i => $p)
                <tr class="wp-row">
                    <td><input class="wp-input" name="rows[{{ $i }}][commodity]" value="{{ $p->commodity }}" placeholder="e.g. Tomato" required></td>
                    <td>
                        <select class="wp-select" name="rows[{{ $i }}][category]">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected($p->category === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input class="wp-input" name="rows[{{ $i }}][unit]" value="{{ $p->unit }}" placeholder="per kg"></td>
                    <td><input class="wp-input wp-num" name="rows[{{ $i }}][min_price]" type="number" step="0.01" min="0" value="{{ $p->min_price }}"></td>
                    <td><input class="wp-input wp-num" name="rows[{{ $i }}][max_price]" type="number" step="0.01" min="0" value="{{ $p->max_price }}"></td>
                    <td><input class="wp-input wp-num" name="rows[{{ $i }}][avg_price]" type="number" step="0.01" min="0" value="{{ $p->avg_price }}"></td>
                    <td><button type="button" class="wp-del-row" title="Remove row">✕</button></td>
                </tr>
                @empty
                {{-- empty — JS will add first row --}}
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 0 .5rem;gap:1rem;flex-wrap:wrap">
            <div style="display:flex;gap:.5rem;align-items:center">
                <label style="font-size:.8rem;color:var(--ka-muted)">Prices for date:</label>
                <input type="date" id="wpDatePicker" value="{{ $date }}"
                    style="padding:.3rem .6rem;border:1px solid var(--ka-border);border-radius:6px;font-size:.82rem"
                    oninput="document.getElementById('wpDate').value=this.value">
            </div>
            <button type="submit" class="ka-btn ka-btn-primary">Save Prices</button>
        </div>
    </form>

    {{-- Auto-update info panel --}}
    <div style="margin-top:1.5rem;padding:1rem 1.25rem;background:rgba(var(--ka-green-rgb,.16,185,129),.06);border:1px solid rgba(var(--ka-green-rgb),0.15);border-radius:10px;display:flex;gap:.75rem;align-items:flex-start">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-top:.15rem;flex-shrink:0;color:var(--ka-green,#10b981)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <div style="font-size:.82rem;color:var(--ka-muted)">
            <strong style="color:var(--ka-text)">Auto roll-forward is ON.</strong>
            Every day at <strong>11:00 AM Sri Lanka time</strong>, if no prices have been entered for today,
            the previous day's prices are automatically copied forward. Update prices any time before 11 AM to override.
        </div>
    </div>

</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var body = document.getElementById('wpBody');
    var cats = @json(\App\Models\WholesalePrice::categories());

    function rowIndex() { return body.querySelectorAll('.wp-row').length; }

    function makeRow(i) {
        var catOpts = cats.map(function(c){ return '<option value="'+c+'">'+c+'</option>'; }).join('');
        var tr = document.createElement('tr');
        tr.className = 'wp-row';
        tr.innerHTML = '<td><input class="wp-input" name="rows['+i+'][commodity]" placeholder="e.g. Carrot" required></td>'
            +'<td><select class="wp-select" name="rows['+i+'][category]">'+catOpts+'</select></td>'
            +'<td><input class="wp-input" name="rows['+i+'][unit]" value="per kg" placeholder="per kg"></td>'
            +'<td><input class="wp-input wp-num" name="rows['+i+'][min_price]" type="number" step="0.01" min="0"></td>'
            +'<td><input class="wp-input wp-num" name="rows['+i+'][max_price]" type="number" step="0.01" min="0"></td>'
            +'<td><input class="wp-input wp-num" name="rows['+i+'][avg_price]" type="number" step="0.01" min="0"></td>'
            +'<td><button type="button" class="wp-del-row" title="Remove">✕</button></td>';
        return tr;
    }

    document.getElementById('wpAddRow').addEventListener('click', function(){
        body.appendChild(makeRow(rowIndex()));
    });

    body.addEventListener('click', function(e){
        if(e.target.classList.contains('wp-del-row')){
            e.target.closest('.wp-row').remove();
            // Re-index names
            body.querySelectorAll('.wp-row').forEach(function(tr, idx){
                tr.querySelectorAll('[name]').forEach(function(el){
                    el.name = el.name.replace(/rows\[\d+\]/, 'rows['+idx+']');
                });
            });
        }
    });

    // Start with one empty row if table is empty
    if(rowIndex() === 0) body.appendChild(makeRow(0));
})();
</script>
<style nonce="{{ $cspNonce ?? '' }}">
.wp-input{width:100%;padding:.35rem .5rem;border:1px solid var(--ka-border,#e2e8f0);border-radius:6px;font-size:.82rem;background:var(--ka-surface,#fff);color:var(--ka-text,#1e293b);transition:border-color .15s}
.wp-input:focus{outline:none;border-color:var(--ka-green,#10b981);box-shadow:0 0 0 2px rgba(16,185,129,.12)}
.wp-select{width:100%;padding:.35rem .4rem;border:1px solid var(--ka-border,#e2e8f0);border-radius:6px;font-size:.82rem;background:var(--ka-surface,#fff);color:var(--ka-text,#1e293b)}
.wp-num{text-align:right}
.wp-del-row{background:none;border:none;cursor:pointer;color:#94a3b8;font-size:.9rem;padding:.25rem .4rem;border-radius:4px;transition:color .15s}
.wp-del-row:hover{color:#ef4444}
.ksd-select{padding:.35rem .5rem;border:1px solid var(--ka-border,#e2e8f0);border-radius:6px;background:var(--ka-surface,#fff);color:var(--ka-text,#1e293b)}
</style>
@endpush
