(function () {
    var btn = document.getElementById('kMenuBtn');
    var nav = document.getElementById('kNavLinks');
    var drawer = document.getElementById('kDrawer');
    var drawerOverlay = document.getElementById('kDrawerOverlay');
    var drawerClose = document.getElementById('kDrawerClose');

    function openDrawer() { drawer.classList.add('open'); drawerOverlay.classList.add('open'); document.body.style.overflow = 'hidden'; btn.setAttribute('aria-expanded', 'true'); }
    function closeDrawer() { drawer.classList.remove('open'); drawerOverlay.classList.remove('open'); document.body.style.overflow = ''; btn.setAttribute('aria-expanded', 'false'); }

    if (btn) btn.addEventListener('click', openDrawer);
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    if (btn && nav) {
        btn.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            btn.textContent = isOpen ? '✕' : '☰';
        });
    }

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
        if (autoplayPauseTimers[track.id]) {
            clearTimeout(autoplayPauseTimers[track.id]);
        }
        autoplayPauseTimers[track.id] = setTimeout(function () {
            startCarouselAutoplay(track);
        }, 6000);
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
        fadeEls.forEach(function (el, i) { el.classList.add('k-fade-in'); el.style.transitionDelay = (i % 6) * 80 + 'ms'; });
        var fadeObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('k-visible'); fadeObs.unobserve(e.target); }
            });
        }, { threshold: 0, rootMargin: '0px 0px 60px 0px' });
        fadeEls.forEach(function (el) { fadeObs.observe(el); });
    }

    document.querySelectorAll('.k-listing-img').forEach(function(el){
        var img = el.querySelector('img');
        if(img){
            if(img.complete && img.naturalWidth > 0){
                el.style.animation = 'none';
            } else {
                img.addEventListener('load', function(){ el.style.animation = 'none'; });
            }
        }
    });
    setTimeout(function(){
        document.querySelectorAll('.k-listing-img').forEach(function(el){
            el.style.animation = 'none';
            var img = el.querySelector('img');
            if(!img || (img.complete && img.naturalWidth === 0)){
                el.classList.add('k-img-failed');
                if(img) img.remove();
            }
        });
        document.querySelectorAll('.k-deal-card-img').forEach(function(el){
            var img = el.querySelector('img');
            if(!img || (img.complete && img.naturalWidth === 0)){
                el.classList.add('k-img-failed');
                if(img) img.remove();
            }
        });
    }, 15000);

    var btt = document.getElementById('kBackTop');
    if (btt) {
        var bttVisible = false;
        window.addEventListener('scroll', function () {
            var show = window.scrollY > 400;
            if (show !== bttVisible) { bttVisible = show; btt.classList.toggle('k-visible', show); }
        }, { passive: true });
        btt.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    }

    var sInput = document.getElementById('kSearchInput');
    var sDrop = document.getElementById('kSearchDropdown');
    var sTimer = null;
    if (sInput && sDrop) {
        sInput.addEventListener('input', function () {
            clearTimeout(sTimer);
            var q = sInput.value.trim();
            if (q.length < 2) { sDrop.classList.remove('open'); return; }
            sTimer = setTimeout(function () {
                fetch('/api/search-suggestions?q=' + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.length) { sDrop.classList.remove('open'); return; }
                        sDrop.innerHTML = data.map(function (item) {
                            return '<a class="k-search-item" href="' + item.url + '">' +
                                '<span class="k-search-item-title">' + item.title + '</span>' +
                                (item.category ? '<span class="k-search-item-cat">' + item.category + '</span>' : '') +
                                '</a>';
                        }).join('');
                        sDrop.classList.add('open');
                    }).catch(function () { sDrop.classList.remove('open'); });
            }, 300);
        });
        sInput.addEventListener('focus', function () {
            if (sDrop.children.length && sInput.value.trim().length >= 2) sDrop.classList.add('open');
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.k-search-wrap')) sDrop.classList.remove('open');
        });
    }

    var dealTimers = document.querySelectorAll('.k-deal-card-timer[data-deal-end]');
    if (dealTimers.length) {
        function pad2(n){return n<10?'0'+n:n}
        function updateDealTimers(){
            var now = new Date();
            dealTimers.forEach(function(el){
                var end = new Date(el.dataset.dealEnd);
                var diff = Math.max(0, end - now);
                var cd = el.querySelector('.k-deal-card-countdown');
                if(!cd) return;
                if(diff <= 0){ cd.textContent = 'Expired'; cd.classList.add('k-deal-urgent'); return; }
                var d = Math.floor(diff/864e5);
                var h = Math.floor(diff%864e5/36e5);
                var m = Math.floor(diff%36e5/6e4);
                var s = Math.floor(diff%6e4/1e3);
                if(d > 0) cd.textContent = d+'d '+pad2(h)+'h '+pad2(m)+'m '+pad2(s)+'s';
                else cd.textContent = pad2(h)+'h '+pad2(m)+'m '+pad2(s)+'s';
                if(d === 0 && h < 6) cd.classList.add('k-deal-urgent');
                else cd.classList.remove('k-deal-urgent');
            });
        }
        updateDealTimers();
        setInterval(updateDealTimers, 1000);
    }
})();

window.kMarkLoadedImages = function() {
    document.querySelectorAll('.k-listing-img img, .k-store-card-v2-logo img').forEach(function(img) {
        if (img.complete && img.naturalWidth > 0) { img.classList.add('k-img-loaded'); }
    });
};
document.addEventListener('load', function(e) {
    var t = e.target;
    if (t.tagName === 'IMG' && (t.closest('.k-listing-img') || t.closest('.k-store-card-v2-logo'))) {
        t.classList.add('k-img-loaded');
    }
}, true);

document.addEventListener('DOMContentLoaded', function() {
    window.kMarkLoadedImages();

    var sp = document.querySelector('.k-scroll-progress');
    if (!sp) { sp = document.createElement('div'); sp.className = 'k-scroll-progress'; document.body.appendChild(sp); }
    window.addEventListener('scroll', function() {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        sp.style.width = h > 0 ? (window.scrollY / h * 100) + '%' : '0';
    }, {passive: true});
});
