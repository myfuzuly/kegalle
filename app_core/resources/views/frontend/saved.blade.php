@extends('layouts.app')

@section('title', 'Saved Listings · Kegalle Marketplace')
@section('meta_description', 'View your saved listings on Kegalle Marketplace. Browse your favorites and find them easily later.')

@push('styles')
<style>
.k-saved-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:8px; }
.k-saved-count { font-size:14px; color:var(--k-text-muted); font-weight:500; }
.k-clear-all-btn { background:none; border:1px solid var(--k-border); color:var(--k-text-muted); padding:8px 16px; border-radius:var(--k-radius); cursor:pointer; font-size:13px; font-weight:600; transition:all .2s; }
.k-clear-all-btn:hover { border-color:var(--k-red); color:var(--k-red); }
.k-saved-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
@media(max-width:900px) { .k-saved-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:560px) { .k-saved-grid { grid-template-columns:1fr; } }
.k-saved-loading { text-align:center; padding:60px 20px; color:var(--k-text-muted); font-size:15px; }
.k-saved-loading .k-spinner { display:inline-block; width:32px; height:32px; border:3px solid var(--k-border); border-top-color:var(--k-primary); border-radius:50%; animation:k-spin .7s linear infinite; margin-bottom:12px; }
@keyframes k-spin { to { transform:rotate(360deg); } }
.k-saved-card-remove { position:absolute; top:8px; right:8px; background:rgba(255,255,255,.92); border:none; border-radius:50%; width:32px; height:32px; cursor:pointer; font-size:16px; line-height:32px; text-align:center; z-index:2; box-shadow:0 1px 4px rgba(0,0,0,.12); transition:background .2s; }
.k-saved-card-remove:hover { background:var(--k-red-light); color:var(--k-red); }
.k-saved-card-wrap { position:relative; }
</style>
@endpush

@section('content')
<div class="container" style="padding-top:12px;padding-bottom:40px">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <span class="current">Saved Listings</span>
    </div>

    <div class="k-content-section">
        <div class="k-saved-header">
            <div>
                <h1 class="k-page-title" style="margin-bottom:4px">Saved Listings</h1>
                <p class="k-page-subtitle k-saved-count" id="savedCount"></p>
            </div>
            <button class="k-clear-all-btn" id="clearAllBtn" style="display:none" onclick="clearAllSaved()">✕ Clear All</button>
        </div>

        <div id="savedContent">
            <div class="k-saved-loading" id="savedLoading">
                <div class="k-spinner"></div>
                <div>Loading your saved listings...</div>
            </div>
        </div>

        <div class="k-empty-state-box" id="emptyState" style="display:none">
            <div style="font-size:48px;margin-bottom:12px">🤍</div>
            <h2 class="k-empty-state-heading">No saved listings yet</h2>
            <p style="color:var(--k-text-muted);margin:8px 0 20px;max-width:400px">Browse and tap the heart / save icon on any listing to save it for later.</p>
            <a href="/listings" class="k-btn k-btn-primary">Browse Listings</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var saved = JSON.parse(localStorage.getItem('k_saved') || '[]');
    var contentEl = document.getElementById('savedContent');
    var loadingEl = document.getElementById('savedLoading');
    var emptyEl = document.getElementById('emptyState');
    var countEl = document.getElementById('savedCount');
    var clearBtn = document.getElementById('clearAllBtn');

    function showEmpty() {
        loadingEl.style.display = 'none';
        contentEl.style.display = 'none';
        emptyEl.style.display = '';
        clearBtn.style.display = 'none';
        countEl.textContent = '';
    }

    if (!saved.length) { showEmpty(); return; }

    var ids = saved.slice(0, 50).join(',');
    fetch('/api/listings/saved?ids=' + encodeURIComponent(ids))
        .then(function(r) { return r.json(); })
        .then(function(listings) {
            if (!listings.length) { showEmpty(); return; }
            countEl.textContent = listings.length + ' saved listing' + (listings.length === 1 ? '' : 's');
            clearBtn.style.display = '';
            var html = '<div class="k-saved-grid">';
            listings.forEach(function(l) {
                html += buildCard(l);
            });
            html += '</div>';
            loadingEl.style.display = 'none';
            contentEl.innerHTML = html;
        })
        .catch(function() {
            loadingEl.innerHTML = '<p style="color:var(--k-red)">Failed to load saved listings. Please try again.</p>';
        });

    function buildCard(l) {
        var imgHtml;
        if (l.image) {
            imgHtml = '<img loading="lazy" src="' + l.image + '" alt="' + esc(l.title) + '" class="k-cover-img" onerror="this.closest(\'.k-listing-img\').classList.add(\'k-img-failed\');this.remove()">';
        } else {
            var icon = l.category_icon || '📦';
            imgHtml = '<div class="k-placeholder-icon"><span>' + icon + '</span><span class="k-ph-label">No Photo</span></div>';
        }

        var tagClass = 'k-tag-new';
        var tagLabel = 'For Sale';
        var t = (l.ad_type || 'sale').toLowerCase();
        if (t.indexOf('rent') !== -1) { tagClass = 'k-tag-rent'; tagLabel = 'For Rent'; }
        else if (t.indexOf('want') !== -1) { tagClass = 'k-tag-wanted'; tagLabel = 'Wanted'; }

        var price = l.price > 0 ? 'LKR ' + Number(l.price).toLocaleString() : 'Contact Seller';

        return '<div class="k-saved-card-wrap">' +
            '<button class="k-saved-card-remove" onclick="removeSaved(\'' + l.id + '\', this)" title="Remove">✕</button>' +
            '<a class="k-listing-card" href="/listings/' + esc(l.slug) + '">' +
                '<div class="k-listing-img">' + imgHtml +
                    '<div class="k-listing-badge"><span class="k-tag ' + tagClass + '">' + tagLabel + '</span></div>' +
                '</div>' +
                '<div class="k-listing-body">' +
                    '<div class="k-listing-title">' + esc(l.title) + '</div>' +
                    '<div class="k-listing-meta">' +
                        '<span class="k-listing-loc">📍 ' + esc(l.location || 'Kegalle') + '</span>' +
                        '<span class="k-listing-time">🕐 ' + esc(l.time_ago || 'Recently') + '</span>' +
                    '</div>' +
                    '<div class="k-listing-footer"><span class="k-price k-price-sm">' + price + '</span></div>' +
                '</div>' +
            '</a></div>';
    }

    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    window.removeSaved = function(id, btn) {
        var s = JSON.parse(localStorage.getItem('k_saved') || '[]');
        s = s.filter(function(x) { return x != id; });
        localStorage.setItem('k_saved', JSON.stringify(s));
        var wrap = btn.closest('.k-saved-card-wrap');
        if (wrap) wrap.remove();
        var remaining = document.querySelectorAll('.k-saved-card-wrap').length;
        countEl.textContent = remaining + ' saved listing' + (remaining === 1 ? '' : 's');
        if (!remaining) showEmpty();
    };

    window.clearAllSaved = function() {
        localStorage.removeItem('k_saved');
        document.querySelectorAll('.k-saved-card-wrap').forEach(function(el) { el.remove(); });
        showEmpty();
    };
})();
</script>
@endpush
