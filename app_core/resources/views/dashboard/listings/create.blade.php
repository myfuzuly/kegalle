@extends('layouts.app')
@section('title','Post Listing - Kegalle')
@push('styles')
<link rel="stylesheet" href="/css/kegalle-dashboard-functional.css?v=2">
@endpush
@section('content')
<section class="kd-wrap">
    <div class="kd-container kd-form-container">
        <div class="kd-head">
            <div>
                <span>Seller Dashboard</span>
                <h1>Post New Listing</h1>
                <p>Add a store product or individual classified ad.</p>
            </div>
        </div>

        <form class="kd-form" method="post" action="/dashboard/listings" enctype="multipart/form-data">
            @csrf
            @if(($stores ?? collect())->count() > 1)
            <label style="margin-bottom:16px;display:block">Post to Store
                <select name="store_id" required>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} · {{ $s->city ?? 'Kegalle' }}</option>
                    @endforeach
                </select>
            </label>
            @elseif($store)
            <div style="background:#e8f5e9;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#2e7d32;font-weight:600">
                Posting as: {{ $store->name }} · {{ $store->city ?? 'Kegalle' }}
            </div>
            @endif
            <div class="kd-form-grid">
                <label>Title
                    <input name="title" required placeholder="Example: iPhone 15 Pro Max">
                </label>

                <label>Category
                    <select name="category_id" id="cf-category-select" required>
                        <option value="">Search or select category...</option>
                        @foreach($categories->whereNull('parent_id') as $parent)
                            <optgroup label="{{ $parent->icon }} {{ $parent->name }}">
                                @foreach($categories->where('parent_id', $parent->id) as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </label>

                <label>Price
                    <input name="price" type="number" min="0" step="0.01" inputmode="decimal" placeholder="Price in LKR">
                </label>
            </div>

            <div id="category-fields-container"></div>

            <label>Description
                <textarea name="description" required placeholder="Describe condition, features, warranty, delivery, etc."></textarea>
            </label>

            <div class="k-photo-encourage">
                <div class="k-photo-encourage-icon">📸</div>
                <div class="k-photo-encourage-text"><strong>Ads with photos get 5x more views!</strong> Upload clear, well-lit photos to sell faster.</div>
            </div>
            <label>Images
                <input type="file" name="images[]" multiple accept="image/*" id="kImagesInput">
                <small>You can upload multiple JPG, PNG or WEBP images.</small>
            </label>
            <div id="imgPreviewGrid" style="display:flex;flex-wrap:wrap;gap:10px"></div>

            <button class="kd-primary" type="submit">Submit for Approval</button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script src="/js/category-fields.js?v=10"></script>
<script>
(function(){
    var form = document.querySelector('.kd-form');
    if(!form) return;

    function showError(input, msg){
        clearError(input);
        input.style.borderColor = '#D32F2F';
        var el = document.createElement('div');
        el.className = 'k-field-error';
        el.style.cssText = 'color:#D32F2F;font-size:12px;margin-top:4px;font-weight:500';
        el.textContent = msg;
        input.parentNode.appendChild(el);
    }

    function clearError(input){
        input.style.borderColor = '';
        var err = input.parentNode.querySelector('.k-field-error');
        if(err) err.remove();
    }

    form.querySelectorAll('input[required],select[required],textarea[required]').forEach(function(el){
        el.addEventListener('blur', function(){
            if(!this.value.trim()) showError(this, 'This field is required');
            else clearError(this);
        });
        el.addEventListener('input', function(){ if(this.value.trim()) clearError(this); });
    });

    var titleInput = form.querySelector('[name="title"]');
    if(titleInput){
        titleInput.addEventListener('blur', function(){
            if(this.value.trim() && this.value.trim().length < 5) showError(this, 'Title must be at least 5 characters');
        });
    }

    var priceInput = form.querySelector('[name="price"]');
    if(priceInput){
        priceInput.addEventListener('blur', function(){
            if(this.value && Number(this.value) < 0) showError(this, 'Price cannot be negative');
            else clearError(this);
        });
    }

    form.addEventListener('submit', function(e){
        var valid = true;
        form.querySelectorAll('input[required],select[required],textarea[required]').forEach(function(el){
            if(!el.value.trim()){ showError(el, 'This field is required'); valid = false; }
        });
        if(!valid){ e.preventDefault(); form.querySelector('.k-field-error')?.closest('label')?.scrollIntoView({behavior:'smooth',block:'center'}); return; }
        var btn = form.querySelector('button[type=submit]');
        if(btn){ btn.disabled = true; btn.textContent = 'Uploading… please wait'; btn.style.opacity = '.7'; }
    });

    // Image previews with remove buttons
    var fileInput = document.getElementById('kImagesInput');
    var grid = document.getElementById('imgPreviewGrid');
    var files = [];
    if(fileInput && grid){
        fileInput.addEventListener('change', function(){
            files = Array.prototype.slice.call(fileInput.files);
            renderPreviews();
        });
    }
    function syncInput(){
        var dt = new DataTransfer();
        files.forEach(function(f){ dt.items.add(f); });
        fileInput.files = dt.files;
    }
    function renderPreviews(){
        grid.innerHTML = '';
        files.forEach(function(f, i){
            if(!f.type.match(/^image\//)) return;
            var url = URL.createObjectURL(f);
            var d = document.createElement('div');
            d.style.cssText = 'position:relative;width:92px;height:92px';
            var img = document.createElement('img');
            img.src = url; img.alt = 'Preview';
            img.style.cssText = 'width:92px;height:92px;object-fit:cover;border-radius:10px;border:1.5px solid #E5E8EF';
            var rm = document.createElement('button');
            rm.type = 'button'; rm.textContent = '×'; rm.setAttribute('aria-label','Remove image');
            rm.style.cssText = 'position:absolute;top:-7px;right:-7px;width:24px;height:24px;border-radius:50%;background:#D32F2F;color:#fff;border:2px solid #fff;cursor:pointer;font-size:13px;line-height:1;font-weight:700';
            rm.addEventListener('click', function(){ files.splice(i,1); syncInput(); renderPreviews(); });
            d.appendChild(img); d.appendChild(rm);
            grid.appendChild(d);
        });
    }
})();
</script>
@endpush
