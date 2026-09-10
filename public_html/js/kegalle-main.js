(function () {
var btn = document.getElementById('kMenuBtn');
var nav = document.getElementById('kNavLinks');
var drawer = document.getElementById('kDrawer');
var drawerOverlay = document.getElementById('kDrawerOverlay');
var drawerClose = document.getElementById('kDrawerClose');
var focusTrapHandler = null;
function openDrawer() {
if (drawer) drawer.classList.add('open');
if (drawerOverlay) drawerOverlay.classList.add('open');
if (nav) nav.classList.add('open');
document.body.style.overflow = 'hidden';
if (btn) { btn.setAttribute('aria-expanded', 'true'); btn.textContent = '✕'; }
if (drawer) {
  var focusable = drawer.querySelectorAll('a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])');
  var first = focusable[0]; var last = focusable[focusable.length - 1];
  if (first) first.focus();
  focusTrapHandler = function(e) {
    if (e.key === 'Escape') { closeDrawer(); return; }
    if (e.key !== 'Tab') return;
    if (!focusable.length) { e.preventDefault(); return; }
    if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } }
    else { if (document.activeElement === last) { e.preventDefault(); first.focus(); } }
  };
  drawer.addEventListener('keydown', focusTrapHandler);
}
}
function closeDrawer() {
if (drawer && focusTrapHandler) { drawer.removeEventListener('keydown', focusTrapHandler); focusTrapHandler = null; }
if (drawer) drawer.classList.remove('open');
if (drawerOverlay) drawerOverlay.classList.remove('open');
if (nav) nav.classList.remove('open');
document.body.style.overflow = '';
if (btn) { btn.setAttribute('aria-expanded', 'false'); btn.textContent = '☰'; btn.focus(); }
}
if (btn) btn.addEventListener('click', openDrawer);
if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);
var subnavBtn = document.getElementById('kSubnavMenuBtn');
var subnavLinks = document.getElementById('kSubnavLinks');
if (subnavBtn && subnavLinks) {
subnavBtn.addEventListener('click', function () {
var isOpen = subnavLinks.classList.toggle('open');
subnavBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
subnavBtn.textContent = isOpen ? '✕ Browse' : '☰ Browse';
});
}
document.addEventListener('click', function (e) {
var prevBtn = e.target.closest('[data-carousel-prev]');
var nextBtn = e.target.closest('[data-carousel-next]');
var trigger = prevBtn || nextBtn;
if (!trigger) return;
var track = document.getElementById(trigger.getAttribute(prevBtn ? 'data-carousel-prev' : 'data-carousel-next'));
if (!track) return;
var scrollAmount = track.clientWidth;
track.scrollBy({ left: prevBtn ? -scrollAmount : scrollAmount, behavior: 'smooth' });
pauseCarouselAutoplay(track);
});
var autoplayTimers = {};
var autoplayPauseTimers = {};
function pauseCarouselAutoplay(track) {
if (autoplayTimers[track.id]) {
clearInterval(autoplayTimers[track.id]);
autoplayTimers[track.id] = null;
}
if (autoplayPauseTimers[track.id]) clearTimeout(autoplayPauseTimers[track.id]);
autoplayPauseTimers[track.id] = setTimeout(function () { startCarouselAutoplay(track); }, 6000);
}
function startCarouselAutoplay(track) {
if (autoplayTimers[track.id]) return;
var item = track.querySelector('.k-carousel-item');
if (!item) return;
var itemWidth = item.offsetWidth + 16;
autoplayTimers[track.id] = setInterval(function () {
var maxScroll = track.scrollWidth - track.clientWidth;
if (maxScroll <= 4) return;
if (track.scrollLeft >= maxScroll - 4) {
track.scrollTo({ left: 0, behavior: 'smooth' });
} else {
track.scrollBy({ left: itemWidth, behavior: 'smooth' });
}
}, 6000);
}
document.querySelectorAll('.k-carousel[id]').forEach(function (track) {
startCarouselAutoplay(track);
track.addEventListener('mouseenter', function () { pauseCarouselAutoplay(track); });
track.addEventListener('mouseleave', function () { startCarouselAutoplay(track); });
track.addEventListener('touchstart', function () { pauseCarouselAutoplay(track); }, { passive: true });
track.addEventListener('focusin', function () { pauseCarouselAutoplay(track); });
track.addEventListener('focusout', function () { startCarouselAutoplay(track); });
});
var fadeEls = document.querySelectorAll('.k-cta-banner, .k-explore-card, .k-cat-pill, .k-blog-card, .k-listing-card, .k-store-card-v2, .k-deal-card-home');
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
fadeEls.forEach(function (el, i) { el.classList.add('k-fade-in'); el.dataset.fi = String(i % 6); });
var fadeObs = new IntersectionObserver(function (entries) {
entries.forEach(function (e) {
if (e.isIntersecting) { e.target.classList.add('k-visible'); fadeObs.unobserve(e.target); }
});
}, { threshold: 0, rootMargin: '0px 0px 60px 0px' });
fadeEls.forEach(function (el) { fadeObs.observe(el); });
}
document.querySelectorAll('.k-listing-img, .k-deal-card-img').forEach(function (el) {
var img = el.querySelector('img');
if (!img) { el.classList.add('k-img-failed'); return; }
if (img.complete) {
if (img.naturalWidth > 0) { el.classList.add('k-img-anim-done'); }
else { el.classList.add('k-img-failed'); img.remove(); }
} else {
img.addEventListener('load', function () { el.classList.add('k-img-anim-done'); });
img.addEventListener('error', function () { el.classList.add('k-img-failed'); img.remove(); });
}
});
var btt = document.getElementById('kBackTop');
var kNav = document.querySelector('.k-navbar');
var scrolled = false;
window.addEventListener('scroll', function () {
var show = window.scrollY > 400;
if (show !== scrolled) { scrolled = show; if (btt) btt.classList.toggle('k-visible', show); if (kNav) kNav.classList.toggle('scrolled', show); }
}, { passive: true });
if (btt) { btt.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); }); }
var sInput = document.getElementById('kSearchInput');
var sDrop = document.getElementById('kSearchDropdown');
var sWrap = sInput && sInput.closest('.k-search-wrap');
var sTimer = null;
var sActive = -1;
function sEsc(s) {
if (s == null) return '';
return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
function sHl(text, q) {
if (!q) return sEsc(text);
var re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
return sEsc(text).replace(re, '<mark>$1</mark>');
}
function sOpen() {
if (sDrop.innerHTML) { sDrop.classList.add('open'); if (sWrap) sWrap.classList.add('k-search-open'); }
}
function sClose() {
sDrop.classList.remove('open');
if (sWrap) sWrap.classList.remove('k-search-open');
sActive = -1;
}
function sRender(data, q) {
if (!data || !data.length) {
sDrop.innerHTML = '<div class="k-search-empty">No results for "<strong>' + sEsc(q) + '</strong>"</div>';
sOpen(); return;
}
var rows = data.slice(0, 8).map(function (item) {
var safeUrl = '';
try { safeUrl = new URL(item.url, location.origin).href; } catch (e) { safeUrl = '/listings'; }
var thumb = item.image
  ? '<img class="k-search-result-thumb" src="' + sEsc(item.image) + '" alt="" loading="lazy" onerror="this.style.display=\'none\'">'
  : '<span class="k-search-result-thumb" style="display:flex;align-items:center;justify-content:center;font-size:18px;">🏷️</span>';
var sub = [];
if (item.location) sub.push('<span>' + sEsc(item.location) + '</span>');
if (item.category) sub.push('<span>' + sEsc(item.category) + '</span>');
return '<a class="k-search-result-item" href="' + sEsc(safeUrl) + '">'
  + thumb
  + '<span class="k-search-result-text">'
  + '<span class="k-search-result-title">' + sHl(item.title, q) + '</span>'
  + (sub.length ? '<span class="k-search-result-sub">' + sub.join('') + '</span>' : '')
  + '</span>'
  + (item.price ? '<span class="k-search-result-price">' + sEsc(item.price) + '</span>' : '')
  + (item.featured ? '<span class="k-search-result-featured">Featured</span>' : '')
  + '</a>';
}).join('');
rows += '<a class="k-search-all" href="/listings?q=' + encodeURIComponent(q) + '">See all results for "<strong>' + sEsc(q) + '</strong>" &rarr;</a>';
sDrop.innerHTML = rows;
sOpen();
}
var sPopular = ['Mobile phones', 'Cars for sale', 'House for rent', 'Electronics', 'Land for sale', 'Jobs in Kegalle', 'Furniture', 'Motorbikes'];
var sHistKey = 'k_search_history';
function sSaveHistory(q) { if (!q) return; try { var h = JSON.parse(localStorage.getItem(sHistKey) || '[]'); h = h.filter(function (x) { return x !== q; }); h.unshift(q); localStorage.setItem(sHistKey, JSON.stringify(h.slice(0, 8))); } catch (e) {} }
function sGetHistory() { try { return JSON.parse(localStorage.getItem(sHistKey) || '[]'); } catch (e) { return []; } }
function sShowEmpty() {
var hist = sGetHistory();
var html = '';
if (hist.length) {
html += '<div class="k-search-section-label">Recent searches</div>';
html += hist.slice(0, 5).map(function (q) { return '<a class="k-search-item k-search-hist-item" href="/listings?q=' + encodeURIComponent(q) + '"><span class="k-search-item-icon">🕐</span><span class="k-search-item-title">' + sEsc(q) + '</span></a>'; }).join('');
}
html += '<div class="k-search-section-label">Popular in Kegalle</div>';
html += sPopular.slice(0, 5).map(function (q) { return '<a class="k-search-item" href="/listings?q=' + encodeURIComponent(q) + '"><span class="k-search-item-icon">🔥</span><span class="k-search-item-title">' + sEsc(q) + '</span></a>'; }).join('');
sDrop.innerHTML = html;
sOpen();
}
if (sInput && sDrop) {
sInput.addEventListener('input', function () {
clearTimeout(sTimer);
sActive = -1;
var q = sInput.value.trim();
if (q.length < 2) { if (q.length === 0) sShowEmpty(); else sClose(); return; }
sDrop.innerHTML = '<div class="k-search-loading"><span></span><span></span><span></span></div>';
sOpen();
sTimer = setTimeout(function () {
fetch('/api/search-suggestions?q=' + encodeURIComponent(q))
.then(function (r) { return r.json(); })
.then(function (data) { sRender(data, q); })
.catch(function () { sClose(); });
}, 280);
});
sInput.addEventListener('focus', function () {
var q = sInput.value.trim();
if (q.length >= 2 && sDrop.innerHTML) sOpen();
else if (q.length === 0) sShowEmpty();
});
var sForm = sInput.closest('form') || (sWrap && sWrap.querySelector('form'));
if (sForm) sForm.addEventListener('submit', function () { sSaveHistory(sInput.value.trim()); });
sInput.addEventListener('keydown', function (e) {
var items = sDrop.querySelectorAll('.k-search-item, .k-search-all');
if (!sDrop.classList.contains('open') || !items.length) return;
if (e.key === 'ArrowDown') { e.preventDefault(); sActive = Math.min(sActive + 1, items.length - 1); }
else if (e.key === 'ArrowUp') { e.preventDefault(); sActive = Math.max(sActive - 1, -1); }
else if (e.key === 'Escape') { sClose(); sInput.blur(); return; }
else if (e.key === 'Enter' && sActive >= 0) { e.preventDefault(); items[sActive].click(); return; }
items.forEach(function (el, i) { el.classList.toggle('k-search-item-active', i === sActive); });
if (sActive >= 0) items[sActive].scrollIntoView({ block: 'nearest' });
});
document.addEventListener('click', function (e) {
if (!e.target.closest('.k-search-wrap')) sClose();
});
document.addEventListener('keydown', function (e) {
if (e.key === 'Escape' && sDrop.classList.contains('open')) sClose();
});
}
var dealTimers = document.querySelectorAll('.k-deal-card-timer[data-deal-end]');
if (dealTimers.length) {
function pad2(n) { return n < 10 ? '0' + n : n; }
function updateDealTimers() {
var now = new Date();
dealTimers.forEach(function (el) {
var end = new Date(el.dataset.dealEnd);
var diff = Math.max(0, end - now);
var cd = el.querySelector('.k-deal-card-countdown');
if (!cd) return;
if (diff <= 0) { cd.textContent = 'Expired'; cd.classList.add('k-deal-urgent'); return; }
var d = Math.floor(diff / 864e5);
var h = Math.floor(diff % 864e5 / 36e5);
var m = Math.floor(diff % 36e5 / 6e4);
var s = Math.floor(diff % 6e4 / 1e3);
cd.textContent = d > 0 ? d + 'd ' + pad2(h) + 'h ' + pad2(m) + 'm ' + pad2(s) + 's' : pad2(h) + 'h ' + pad2(m) + 'm ' + pad2(s) + 's';
if (d === 0 && h < 6) cd.classList.add('k-deal-urgent');
else cd.classList.remove('k-deal-urgent');
});
}
updateDealTimers();
setInterval(updateDealTimers, 1000);
}
})();
window.kMarkLoadedImages = function () {
document.querySelectorAll('.k-listing-img img, .k-store-card-v2-logo img').forEach(function (img) {
if (img.complete && img.naturalWidth > 0) img.classList.add('k-img-loaded');
});
};
document.addEventListener('load', function (e) {
var t = e.target;
if (t.tagName === 'IMG' && (t.closest('.k-listing-img') || t.closest('.k-store-card-v2-logo'))) {
t.classList.add('k-img-loaded');
}
}, true);
document.addEventListener('DOMContentLoaded', function () {
window.kMarkLoadedImages();
var sp = document.querySelector('.k-scroll-progress');
if (!sp) { sp = document.createElement('div'); sp.className = 'k-scroll-progress'; document.body.appendChild(sp); }
window.addEventListener('scroll', function () {
var h = document.documentElement.scrollHeight - window.innerHeight;
/* scroll progress driven by CSS animation-timeline */
}, { passive: true });
document.addEventListener('submit', function (e) {
var form = e.target;
var submitBtn = form.querySelector('button[type="submit"], .kd-primary[type="submit"], .k-btn-primary[type="submit"], button:not([type])');
if (submitBtn && !submitBtn.classList.contains('k-btn-loading')) {
submitBtn.classList.add('k-btn-loading');
submitBtn.disabled = true;
setTimeout(function () { submitBtn.classList.remove('k-btn-loading'); submitBtn.disabled = false; }, 10000);
}
});
document.querySelectorAll('.kd-form').forEach(function (form) {
form.setAttribute('novalidate', '');
form.addEventListener('submit', function (e) {
var ok = true;
form.querySelectorAll('.ka-field-error').forEach(function (f) { f.classList.remove('ka-field-error'); });
form.querySelectorAll('.ka-field-error-msg').forEach(function (m) { m.remove(); });
form.querySelectorAll('[required]').forEach(function (inp) {
if (!(inp.value || '').trim()) {
ok = false;
var wrap = inp.closest('label') || inp.parentElement;
wrap.classList.add('ka-field-error');
var msg = document.createElement('small');
msg.className = 'ka-field-error-msg';
msg.textContent = 'This field is required';
inp.insertAdjacentElement('afterend', msg);
}
});
form.querySelectorAll('[type="email"]').forEach(function (inp) {
var val = (inp.value || '').trim();
if (val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
ok = false;
var wrap = inp.closest('label') || inp.parentElement;
wrap.classList.add('ka-field-error');
var msg = document.createElement('small');
msg.className = 'ka-field-error-msg';
msg.textContent = 'Please enter a valid email';
inp.insertAdjacentElement('afterend', msg);
}
});
if (!ok) { e.preventDefault(); var first = form.querySelector('.ka-field-error-msg'); if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
});
form.addEventListener('input', function (e) {
var wrap = e.target.closest('label') || e.target.parentElement;
if (wrap) { wrap.classList.remove('ka-field-error'); var m = wrap.querySelector('.ka-field-error-msg'); if (m) m.remove(); }
});
});
var waBtn = document.querySelector('.k-whatsapp-btn');
var footer = document.querySelector('.k-footer');
if (waBtn && footer) {
var waHidden = false;
window.addEventListener('scroll', function () {
var fRect = footer.getBoundingClientRect();
var wRect = waBtn.getBoundingClientRect();
var overlap = wRect.bottom > fRect.top && wRect.top < fRect.bottom;
if (overlap !== waHidden) { waHidden = overlap; waBtn.classList.toggle('k-wa-hidden', overlap); }
}, { passive: true });
}
var nlForm = document.getElementById('kNewsletterForm');
if (nlForm) {
nlForm.addEventListener('submit', function (ev) {
ev.preventDefault();
var emailEl = nlForm.querySelector('[name=email]');
var btn = nlForm.querySelector('button[type=submit]');
if (!emailEl || !emailEl.value) return;
btn.disabled = true; btn.textContent = '…';
var csrf = document.querySelector('meta[name=csrf-token]');
fetch('/newsletter/subscribe', {
method: 'POST',
headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '' },
body: JSON.stringify({ email: emailEl.value }),
}).then(function () {
emailEl.value = ''; btn.textContent = 'Subscribe'; btn.disabled = false;
if (window.kToast) kToast('Thanks! You\'re subscribed.', 'success');
}).catch(function () {
btn.textContent = 'Subscribe'; btn.disabled = false;
if (window.kToast) kToast('Something went wrong. Please try again.', 'error');
});
});
}
});

/* Hero slider is initialised in home.blade.php to avoid conflicts */