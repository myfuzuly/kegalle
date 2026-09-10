// ─── Frontend JS entry point ─────────────────────────────────────────────────
import './kegalle-main.js'
import { createApp } from 'vue'
import SearchAutocomplete from './components/SearchAutocomplete.vue'

// Mount search autocomplete Vue island
const searchEl = document.getElementById('k-search-vue')
if (searchEl) {
    createApp(SearchAutocomplete, {
        initialQ: searchEl.dataset.q || ''
    }).mount(searchEl)
}

// ── CSRF token global ────────────────────────────────────────────────────────
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? ''

// ── Dark mode ────────────────────────────────────────────────────────────────
;(function () {
    var html = document.documentElement
    var btn  = document.getElementById('kDarkToggle')
    var stored = localStorage.getItem('k_theme')
    function apply(theme) {
        html.setAttribute('data-theme', theme)
        if (btn) btn.textContent = theme === 'dark' ? '☀️' : '🌙'
        localStorage.setItem('k_theme', theme)
    }
    if (stored) apply(stored)
    if (btn) btn.addEventListener('click', function () {
        apply(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark')
    })
})()

// ── Saved listings badge ─────────────────────────────────────────────────────
;(function () {
    var s = JSON.parse(localStorage.getItem('k_saved') || '[]')
    var c = s.length
    if (!c) return
    var b1 = document.getElementById('kNavSavedBadge')
    var b2 = document.getElementById('kDrawerSavedBadge')
    if (b1) { b1.textContent = c; b1.style.display = '' }
    if (b2) { b2.textContent = c; b2.style.display = 'inline' }
})()

// ── Toast utility ────────────────────────────────────────────────────────────
window.kToast = function (msg, type) {
    var wrap = document.getElementById('k-toast-wrap')
    if (!wrap) return
    var t = document.createElement('div')
    t.className = 'k-toast k-toast-' + type
    t.setAttribute('role', 'alert')
    t.innerHTML = '<span class="k-toast-msg">' + msg + '</span>'
        + '<button class="k-toast-close" onclick="this.parentNode.remove()" aria-label="Close">✕</button>'
    wrap.appendChild(t)
    setTimeout(function () { t.classList.add('k-toast-show') }, 10)
    setTimeout(function () { t.classList.remove('k-toast-show'); setTimeout(function () { t.remove() }, 300) }, 5000)
}

// ── Subnav "More" dropdown ───────────────────────────────────────────────────
;(function () {
    var btn = document.getElementById('kMoreBtn')
    var dd  = document.getElementById('kSubnavDropdown')
    if (!btn || !dd) return
    var skip = false
    btn.addEventListener('mousedown', function () { skip = true })
    btn.addEventListener('click', function () {
        var open = dd.classList.toggle('open')
        btn.setAttribute('aria-expanded', open)
    })
    document.addEventListener('click', function (e) {
        if (skip) { skip = false; return }
        if (dd.contains(e.target)) return
        dd.classList.remove('open')
        btn.setAttribute('aria-expanded', 'false')
    })
})()

// ── Subnav tab-index sync (hide from tab order when collapsed on mobile) ─────
;(function () {
    var nav   = document.getElementById('kSubnav')
    var links = nav ? nav.querySelectorAll('a') : []
    function syncTabIndex() {
        var isMobile = window.innerWidth < 960
        var isOpen   = nav && nav.classList.contains('open')
        var hidden   = isMobile && !isOpen
        links.forEach(function (a) { a.tabIndex = hidden ? -1 : 0 })
        if (nav) nav.setAttribute('aria-hidden', hidden ? 'true' : 'false')
    }
    syncTabIndex()
    window.addEventListener('resize', syncTabIndex)
    var btn = document.getElementById('kSubnavMenuBtn')
    if (btn) btn.addEventListener('click', function () { setTimeout(syncTabIndex, 50) })
})()

// ── Lazy-image fade-in ───────────────────────────────────────────────────────
;(function () {
    if (!('IntersectionObserver' in window)) return
    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
        if (img.complete) { img.classList.add('k-img-loaded'); return }
        img.addEventListener('load', function () { img.classList.add('k-img-loaded') })
    })
})()

