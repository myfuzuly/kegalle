{{-- v2 --}}
{{--
  Category Picker Partial — main-categories only, card grid, multi-select
  Required variables:
    $categories   — collection of root categories with children eager-loaded
    $selectedCats — array of selected category IDs
  Optional:
    $pickerId     — id attribute for the picker div (default: storeCategoryPicker)
    $pickerSingle — bool: true = single-select radio, false = multi-select checkbox
    $pickerName   — override the input name
--}}
@php
    $pickerId     = $pickerId ?? 'storeCategoryPicker';
    $pickerSingle = $pickerSingle ?? false;
    $pickerName   = $pickerName ?? ($pickerSingle ? 'category_id' : 'categories[]');
    $pickerType   = $pickerSingle ? 'radio' : 'checkbox';
    $selectedCatsArr = is_array($selectedCats ?? []) ? ($selectedCats ?? []) : [$selectedCats ?? ''];
    $mainCats = $categories->filter(fn($c) => !$c->parent_id);
@endphp



<input type="text" id="scpSearch{{ $pickerId }}" class="scp-search" placeholder="Search categories…" autocomplete="off">

@php $selCount = count(array_filter($selectedCatsArr)); @endphp
<div class="scp-count" id="scpCount{{ $pickerId }}" style="{{ $selCount ? '' : 'display:none' }}">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="scpCountNum{{ $pickerId }}">{{ $selCount }}</span> selected
</div>

<div class="scp-grid" id="{{ $pickerId }}">
    @foreach($mainCats as $cat)
    <label class="scp-card {{ in_array($cat->id, $selectedCatsArr) ? 'scp-selected' : '' }}" data-name="{{ strtolower($cat->name) }}">
        <input type="{{ $pickerType }}" name="{{ $pickerName }}" value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCatsArr) ? 'checked' : '' }}>
        <span class="scp-icon">{{ $cat->icon ?? '🏷' }}</span>
        <span class="scp-name">{{ $cat->name }}</span>
        <span class="scp-check">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
        </span>
    </label>
    @endforeach
    <div class="scp-no-results" id="scpNoRes{{ $pickerId }}" style="display:none">No categories match</div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var grid   = document.getElementById('{{ $pickerId }}');
    var search = document.getElementById('scpSearch{{ $pickerId }}');
    var noRes  = document.getElementById('scpNoRes{{ $pickerId }}');
    var countWrap = document.getElementById('scpCount{{ $pickerId }}');
    var countNum  = document.getElementById('scpCountNum{{ $pickerId }}');

    function updateCount(){
        var n = grid.querySelectorAll('input:checked').length;
        if(countNum) countNum.textContent = n;
        if(countWrap) countWrap.style.display = n ? '' : 'none';
    }

    grid.addEventListener('click', function(e){
        var card = e.target.closest('.scp-card');
        if(!card) return;
        var cb = card.querySelector('input');
        if(!cb) return;
        // Browser already toggled cb.checked via label click — read it, don't flip again
        if('{{ $pickerType }}' === 'radio'){
            grid.querySelectorAll('.scp-card').forEach(function(c){ c.classList.remove('scp-selected'); });
        }
        card.classList.toggle('scp-selected', cb.checked);
        updateCount();
    });

    if(search){
        search.addEventListener('input', function(){
            var q = this.value.toLowerCase().trim();
            var cards = grid.querySelectorAll('.scp-card');
            var found = 0;
            cards.forEach(function(c){
                var match = !q || (c.dataset.name||'').indexOf(q) !== -1;
                c.style.display = match ? '' : 'none';
                if(match) found++;
            });
            noRes.style.display = found ? 'none' : '';
        });
    }

    updateCount();
})();
</script>
