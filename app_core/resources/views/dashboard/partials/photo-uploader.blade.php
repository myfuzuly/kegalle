{{--
  Dashboard Photo Uploader — slot-based drag-and-drop
  Variables:
    $existingImages  — collection of image models (optional, for edit)
    $maxSlots        — max images (default 6)
--}}
@php
    $maxSlots       = $maxSlots       ?? 6;
    $existingImages = $existingImages ?? collect();
@endphp

<div class="kap-section">

    {{-- Tip banner --}}
    <div class="kap-tip">
        <span class="fs-22">⭐</span>
        <div><strong>Ads with photos get 5× more views.</strong> Upload clear, well-lit photos — main photo first.</div>
    </div>

    {{-- Hidden file input --}}
    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
           id="kapInput" class="hidden">

    {{-- Primary image hidden radio container (existing images) --}}
    <div id="kapPrimaryWrap" class="hidden"></div>

    {{-- Primary index for new uploads --}}
    <input type="hidden" name="new_primary_index" id="kapNewPrimaryIndex" value="0">

    {{-- Slot grid --}}
    <div class="kap-grid" id="kapGrid">

        {{-- Existing images (edit mode) --}}
        @foreach($existingImages as $img)
        @php $isPrimary = $img->is_primary || ($loop->first && !$existingImages->contains('is_primary', true)); @endphp
        <div class="kap-slot kap-has-img {{ $isPrimary ? 'kap-is-main' : '' }}"
             data-exist="{{ $img->id }}" id="kapExist{{ $img->id }}">

            <img src="{{ asset('storage/'.ltrim($img->path,'/')) }}" alt="Photo {{ $loop->iteration }}">

            {{-- MAIN badge --}}
            <span class="kap-main-badge" id="kapBadge{{ $img->id }}" @class(['hidden' => !$isPrimary])>★ Main</span>

            {{-- Hidden radio --}}
            <input type="radio" name="primary_image_id" value="{{ $img->id }}"
                   @checked($isPrimary) class="hidden" class="kap-radio-input" id="kapRadio{{ $img->id }}">

            {{-- Delete checkbox --}}
            <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="hidden" class="kap-del-chk" id="kapDelChk{{ $img->id }}">

            {{-- Delete button --}}
            <button type="button" class="kap-del-btn" data-del-for="{{ $img->id }}" title="Remove this photo">✕</button>

            {{-- Bottom action bar --}}
            <div class="kap-action-bar">
                <button type="button"
                        class="kap-set-main-btn {{ $isPrimary ? 'kap-is-main-btn' : '' }}"
                        data-set-main="{{ $img->id }}"
                        id="kapSetMain{{ $img->id }}"
                        title="Set as featured / main photo">
                    <span class="kap-star">{{ $isPrimary ? '★' : '☆' }}</span>
                    {{ $isPrimary ? 'Main Photo' : 'Set as Main' }}
                </button>
            </div>

        </div>
        @endforeach

        {{-- New-file drop slots --}}
        @php $newSlots = max(0, $maxSlots - $existingImages->count()); @endphp
        @for($i = 0; $i < $newSlots; $i++)
        <div class="kap-slot kap-new-slot" data-slot="{{ $i }}">
            <div class="kap-slot-icon">+</div>
            <div class="kap-slot-label">
                {{ ($existingImages->isEmpty() && $i === 0) ? 'Main photo' : 'Photo '.($existingImages->count() + $i + 1) }}
            </div>
        </div>
        @endfor

    </div>

    <div class="kap-hint">JPG, PNG, WEBP · up to {{ $maxSlots }} photos · max 4 MB each</div>

    @if($existingImages->count())
    <div class="kap-legend">
        <span><span class="dot-green"></span> Main display photo</span>
        <span><span class="dot-red"></span> Marked for removal</span>
    </div>
    @endif

</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    var newSlots      = Array.from(document.querySelectorAll('.kap-new-slot'));
    var files         = newSlots.map(function () { return null; });
    var input         = document.getElementById('kapInput');
    var newPrimaryIdx = document.getElementById('kapNewPrimaryIndex');
    var hasExisting   = {{ $existingImages->isEmpty() ? 'false' : 'true' }};
    var mainNewSlot   = 0; // which new slot is currently "main"

    /* ── new-file slots: render ── */
    function render() {
        newSlots.forEach(function (slot, i) {
            // Clear dynamic children
            ['img.kap-new-img','.kap-rm','.kap-main-badge','.kap-action-bar'].forEach(function(sel){
                var el = slot.querySelector(sel);
                if(el) el.remove();
            });
            var icon  = slot.querySelector('.kap-slot-icon');
            var label = slot.querySelector('.kap-slot-label');
            if (icon)  icon.style.display  = '';
            if (label) label.style.display = '';

            if (files[i]) {
                slot.classList.add('kap-has-img');
                slot.style.border = (i === mainNewSlot && !hasExisting) ? '2.5px solid #16a34a' : '2px solid #22c55e';
                if (icon)  icon.style.display  = 'none';
                if (label) label.style.display = 'none';

                var img = document.createElement('img');
                img.className = 'kap-new-img';
                img.src = URL.createObjectURL(files[i]);
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block';
                slot.appendChild(img);

                // Main badge
                if (!hasExisting && i === mainNewSlot) {
                    var badge = document.createElement('span');
                    badge.className = 'kap-main-badge';
                    badge.textContent = '★ Main';
                    slot.appendChild(badge);
                }

                // Delete button
                var rm = document.createElement('button');
                rm.type = 'button'; rm.className = 'kap-rm'; rm.title = 'Remove'; rm.textContent = '×';
                rm.addEventListener('click', function (e) {
                    e.stopPropagation();
                    files.splice(i, 1);
                    while (files.length < newSlots.length) files.push(null);
                    if (mainNewSlot >= i) { mainNewSlot = Math.max(0, mainNewSlot - 1); }
                    if (newPrimaryIdx) newPrimaryIdx.value = mainNewSlot;
                    syncInput(); render();
                });
                slot.appendChild(rm);

                // Action bar with "Set as Main" (only when no existing images)
                if (!hasExisting) {
                    var bar = document.createElement('div');
                    bar.className = 'kap-action-bar';
                    var isMain = (i === mainNewSlot);
                    bar.innerHTML = '<button type="button" class="kap-set-main-btn' + (isMain ? ' kap-is-main-btn' : '') + '" data-new-slot="' + i + '">'
                        + '<span class="kap-star">' + (isMain ? '★' : '☆') + '</span> '
                        + (isMain ? 'Main Photo' : 'Set as Main')
                        + '</button>';
                    bar.querySelector('.kap-set-main-btn').addEventListener('click', function(e){
                        e.stopPropagation();
                        mainNewSlot = i;
                        if (newPrimaryIdx) newPrimaryIdx.value = i;
                        // Also clear any existing-image primary
                        document.querySelectorAll('.kap-radio-input').forEach(function(r){ r.checked = false; });
                        document.querySelectorAll('[data-set-main]').forEach(function(b){
                            b.classList.remove('kap-is-main-btn');
                            b.innerHTML = '<span class="kap-star">☆</span> Set as Main';
                            var s = b.closest('.kap-slot');
                            if(s){ s.classList.remove('kap-is-main'); var bg=s.querySelector('.kap-main-badge'); if(bg) bg.style.display='none'; }
                        });
                        render();
                    });
                    slot.appendChild(bar);
                }
            } else {
                slot.classList.remove('kap-has-img');
                slot.style.border = '';
            }
        });
        syncInput();
    }

    function addFiles(list) {
        list.forEach(function (f) {
            if (!f.type.match(/^image\//)) return;
            for (var j = 0; j < newSlots.length; j++) {
                if (!files[j]) { files[j] = f; break; }
            }
        });
        render();
    }

    function syncInput() {
        var dt = new DataTransfer();
        files.forEach(function (f) { if (f) dt.items.add(f); });
        input.files = dt.files;
    }

    newSlots.forEach(function (slot, i) {
        slot.addEventListener('click', function () {
            if (files[i]) return;
            var tmp = document.createElement('input');
            tmp.type = 'file'; tmp.accept = 'image/jpeg,image/png,image/webp'; tmp.multiple = true;
            tmp.addEventListener('change', function () { addFiles(Array.from(tmp.files)); });
            tmp.click();
        });
        slot.addEventListener('dragover',  function (e) { e.preventDefault(); slot.style.borderColor = '#1b5e20'; });
        slot.addEventListener('dragleave', function ()  { if (!files[i]) slot.style.borderColor = ''; });
        slot.addEventListener('drop',      function (e) { e.preventDefault(); addFiles(Array.from(e.dataTransfer.files)); });
    });

    /* ── Set as Main buttons ── */
    document.querySelectorAll('.kap-set-main-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var imgId = btn.dataset.setMain;
            if (btn.classList.contains('kap-is-main-btn')) return; // already main

            // Reset all existing-image buttons + badges
            document.querySelectorAll('.kap-set-main-btn').forEach(function (b) {
                b.classList.remove('kap-is-main-btn');
                b.innerHTML = '<span class="kap-star">☆</span> Set as Main';
                var slot = b.closest('.kap-slot');
                if (slot) {
                    slot.classList.remove('kap-is-main');
                    var badge = slot.querySelector('.kap-main-badge');
                    if (badge) badge.style.display = 'none';
                }
            });
            document.querySelectorAll('.kap-radio-input').forEach(function (r) { r.checked = false; });

            // Set this one as main
            btn.classList.add('kap-is-main-btn');
            btn.innerHTML = '<span class="kap-star">★</span> Main Photo';

            var slot = btn.closest('.kap-slot');
            if (slot) {
                slot.classList.add('kap-is-main');
                var badge = slot.querySelector('.kap-main-badge');
                if (badge) badge.style.display = '';
            }

            var radio = document.getElementById('kapRadio' + imgId);
            if (radio) radio.checked = true;
        });
    });

    /* ── Delete toggle ── */
    document.querySelectorAll('.kap-del-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var id   = btn.dataset.delFor;
            var chk  = document.getElementById('kapDelChk' + id);
            var slot = btn.closest('.kap-slot');
            if (chk) {
                chk.checked = !chk.checked;
                slot.classList.toggle('kap-deleted', chk.checked);
                btn.classList.toggle('active', chk.checked);
                // If deleting the main photo, clear main selection
                var radio = document.getElementById('kapRadio' + id);
                if (chk.checked && radio && radio.checked) {
                    radio.checked = false;
                    var mainBtn = document.getElementById('kapSetMain' + id);
                    if (mainBtn) {
                        mainBtn.classList.remove('kap-is-main-btn');
                        mainBtn.innerHTML = '<span class="kap-star">☆</span> Set as Main';
                    }
                    slot.classList.remove('kap-is-main');
                    var badge = slot.querySelector('.kap-main-badge');
                    if (badge) badge.style.display = 'none';
                }
            }
        });
    });
})();
</script>
