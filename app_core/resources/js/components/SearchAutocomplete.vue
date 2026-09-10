<template>
  <div class="ksa-wrap" :class="{ 'ksa-open': isOpen, 'ksa-mobile-open': mobileOpen }" ref="wrap">
    <!-- Mobile overlay backdrop -->
    <div v-if="mobileOpen" class="ksa-backdrop" @click="close" />

    <!-- Search form -->
    <form action="/listings" method="GET" autocomplete="off" @submit.prevent="submit" class="ksa-form">
      <button type="button" class="ksa-back-btn" v-if="mobileOpen" @click="close" aria-label="Close search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      </button>
      <span class="k-search-icon" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </span>
      <input
        ref="inputEl"
        class="k-search-input"
        type="text"
        name="q"
        :value="query"
        @input="onInput"
        @focus="onFocus"
        @keydown="onKeydown"
        placeholder="Search for anything in Kegalle..."
        aria-label="Search listings"
        aria-autocomplete="list"
        aria-haspopup="listbox"
        aria-controls="ksa-dropdown"
        :aria-expanded="isOpen"
        autocomplete="off"
      />
      <button v-if="query" type="button" class="ksa-clear-btn" @click="clearQuery" aria-label="Clear search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      <button class="k-search-btn" type="submit" aria-label="Submit search">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Search
      </button>
    </form>

    <!-- Dropdown -->
    <div v-show="isOpen" class="ksa-dropdown" id="ksa-dropdown" role="listbox">

      <!-- Loading -->
      <div v-if="loading" class="ksa-loading">
        <span class="ksa-spinner"></span> Searching…
      </div>

      <!-- Recent searches (shown when focused, no query) -->
      <template v-else-if="!query && recents.length">
        <div class="ksa-section-label">
          <span>Recent Searches</span>
          <button type="button" class="ksa-clear-recents" @click="clearRecents">Clear</button>
        </div>
        <div
          v-for="(r, i) in recents"
          :key="'r'+i"
          class="ksa-item ksa-recent-item"
          :class="{ 'ksa-active': activeIdx === i }"
          role="option"
          @mousedown.prevent="selectRecent(r)"
        >
          <span class="ksa-recent-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </span>
          <span class="ksa-recent-text">{{ r }}</span>
        </div>
      </template>

      <!-- Suggestions -->
      <template v-else-if="query && results.length">
        <div class="ksa-section-label">Results</div>
        <a
          v-for="(r, i) in results"
          :key="r.url"
          :href="r.url"
          class="ksa-item ksa-result-item"
          :class="{ 'ksa-active': activeIdx === i }"
          role="option"
          @mousedown.prevent="selectResult(r)"
        >
          <div class="ksa-thumb">
            <img v-if="r.image" :src="r.image" :alt="r.title" loading="lazy" />
            <span v-else class="ksa-thumb-icon">🛒</span>
          </div>
          <div class="ksa-item-body">
            <span class="ksa-item-title">{{ r.title }}</span>
            <span class="ksa-item-meta">
              <span v-if="r.category">{{ r.category }}</span>
              <span v-if="r.category && r.location"> · </span>
              <span v-if="r.location">{{ r.location }}</span>
            </span>
          </div>
          <span v-if="r.price" class="ksa-item-price">{{ r.price }}</span>
        </a>
        <a :href="'/listings?q='+encodeURIComponent(query)" class="ksa-view-all" @mousedown.prevent="goAll">
          See all results for "{{ query }}"
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </template>

      <!-- No results -->
      <div v-else-if="query && !loading && searched" class="ksa-empty">
        <span class="ksa-empty-icon">🔍</span>
        <span>No results for "<strong>{{ query }}</strong>"</span>
        <a :href="'/listings?q='+encodeURIComponent(query)" class="ksa-browse-link">Browse all listings →</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({ initialQ: { type: String, default: '' } })

const wrap     = ref(null)
const inputEl  = ref(null)
const query    = ref(props.initialQ)
const results  = ref([])
const loading  = ref(false)
const searched = ref(false)
const activeIdx = ref(-1)
const mobileOpen = ref(false)
const RECENT_KEY = 'ksa_recents'
const recents  = ref(JSON.parse(localStorage.getItem(RECENT_KEY) || '[]'))

const isOpen = computed(() =>
  (query.value.length >= 2 && (loading.value || results.value.length > 0 || searched.value)) ||
  (!query.value && recents.value.length > 0 && document.activeElement === inputEl.value)
)

let timer = null

function onInput(e) {
  query.value = e.target.value
  activeIdx.value = -1
  searched.value = false
  clearTimeout(timer)
  if (query.value.length < 2) { results.value = []; return }
  loading.value = true
  timer = setTimeout(fetchSuggestions, 220)
}

function onFocus() {
  if (window.innerWidth < 640) mobileOpen.value = true
  // Show recents if no query
  if (!query.value && recents.value.length) activeIdx.value = -1
}

async function fetchSuggestions() {
  try {
    const r = await fetch('/api/search-suggestions?q=' + encodeURIComponent(query.value))
    results.value = r.ok ? await r.json() : []
  } catch { results.value = [] }
  loading.value = false
  searched.value = true
}

function onKeydown(e) {
  const list = query.value ? results.value : recents.value
  if (e.key === 'ArrowDown') {
    e.preventDefault()
    activeIdx.value = Math.min(activeIdx.value + 1, list.length - 1)
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    activeIdx.value = Math.max(activeIdx.value - 1, -1)
  } else if (e.key === 'Enter') {
    if (activeIdx.value >= 0) {
      e.preventDefault()
      if (query.value && results.value[activeIdx.value]) selectResult(results.value[activeIdx.value])
      else if (!query.value && recents.value[activeIdx.value]) selectRecent(recents.value[activeIdx.value])
    } else {
      submit()
    }
  } else if (e.key === 'Escape') {
    close()
  }
}

function submit() {
  if (!query.value.trim()) return
  saveRecent(query.value.trim())
  window.location = '/listings?q=' + encodeURIComponent(query.value.trim())
}

function selectResult(r) {
  saveRecent(r.title)
  window.location = r.url
}

function selectRecent(text) {
  query.value = text
  inputEl.value.value = text
  fetchSuggestions()
}

function goAll() {
  saveRecent(query.value.trim())
  window.location = '/listings?q=' + encodeURIComponent(query.value.trim())
}

function saveRecent(text) {
  let r = JSON.parse(localStorage.getItem(RECENT_KEY) || '[]')
  r = [text, ...r.filter(x => x !== text)].slice(0, 6)
  localStorage.setItem(RECENT_KEY, JSON.stringify(r))
  recents.value = r
}

function clearRecents() {
  localStorage.removeItem(RECENT_KEY)
  recents.value = []
}

function clearQuery() {
  query.value = ''
  results.value = []
  searched.value = false
  inputEl.value?.focus()
}

function close() {
  mobileOpen.value = false
  results.value = []
  searched.value = false
  inputEl.value?.blur()
}

function onClickOutside(e) {
  if (wrap.value && !wrap.value.contains(e.target)) {
    mobileOpen.value = false
    results.value = []
    searched.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', onClickOutside)
  if (props.initialQ) { query.value = props.initialQ }
})
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))
</script>

<style scoped>
.ksa-wrap { position: relative; flex: 1; max-width: 780px; min-width: 240px; }
.ksa-form { display: flex; align-items: center; background: #fff; border: 2px solid #e5e8ef; border-radius: 50px; padding: 0 6px 0 16px; gap: 6px; height: 46px; transition: border-color .18s, box-shadow .18s; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.ksa-form:focus-within { background: #fff; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,.12), 0 2px 8px rgba(0,0,0,.08); }
.ksa-clear-btn { background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px; display: flex; align-items: center; border-radius: 50%; transition: color .15s, background .15s; }
.ksa-clear-btn:hover { color: #374151; background: #f3f4f6; }
.ksa-back-btn { background: none; border: none; cursor: pointer; color: #374151; padding: 4px; display: flex; align-items: center; flex-shrink: 0; }

/* Dropdown */
.ksa-dropdown { position: absolute; top: calc(100% + 8px); left: 0; right: 0; background: #fff; border: 1px solid #e5e8ef; border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,.13); z-index: 9999; overflow: hidden; max-height: 480px; overflow-y: auto; }
.ksa-section-label { display: flex; align-items: center; justify-content: space-between; font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; padding: 12px 16px 8px; }
.ksa-clear-recents { background: none; border: none; cursor: pointer; font-size: 11px; color: #6b7280; padding: 2px 6px; border-radius: 4px; }
.ksa-clear-recents:hover { background: #f3f4f6; color: #374151; }

/* Items */
.ksa-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; text-decoration: none; color: inherit; transition: background .12s; }
.ksa-item:hover, .ksa-active { background: #f0fdf4; }
.ksa-recent-icon { color: #9ca3af; flex-shrink: 0; }
.ksa-recent-text { font-size: 14px; color: #374151; }
.ksa-thumb { width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #f3f4f6; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.ksa-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ksa-item-body { flex: 1; min-width: 0; }
.ksa-item-title { display: block; font-size: 14px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ksa-item-meta { display: block; font-size: 12px; color: #6b7280; margin-top: 1px; }
.ksa-item-price { font-size: 13px; font-weight: 700; color: #2e7d32; white-space: nowrap; flex-shrink: 0; }
.ksa-view-all { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 12px 16px; font-size: 13px; font-weight: 600; color: #2e7d32; text-decoration: none; border-top: 1px solid #f3f4f6; transition: background .12s; }
.ksa-view-all:hover { background: #f0fdf4; }

/* Loading */
.ksa-loading { display: flex; align-items: center; gap: 10px; padding: 16px; font-size: 13px; color: #6b7280; }
.ksa-spinner { width: 16px; height: 16px; border: 2px solid #e5e7eb; border-top-color: #2e7d32; border-radius: 50%; animation: ksa-spin .6s linear infinite; flex-shrink: 0; }
@keyframes ksa-spin { to { transform: rotate(360deg); } }

/* Empty */
.ksa-empty { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 24px 16px; font-size: 14px; color: #6b7280; text-align: center; }
.ksa-empty-icon { font-size: 28px; }
.ksa-browse-link { font-size: 13px; color: #2e7d32; text-decoration: none; margin-top: 4px; }

/* Mobile full-screen */
@media (max-width: 639px) {
  .ksa-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 9998; }
  .ksa-wrap.ksa-mobile-open { position: fixed; inset: 0; z-index: 9999; background: #fff; padding: 12px 16px; max-width: 100%; }
  .ksa-wrap.ksa-mobile-open .ksa-form { border-radius: 10px; }
  .ksa-wrap.ksa-mobile-open .ksa-dropdown { position: static; border: none; box-shadow: none; border-radius: 0; max-height: calc(100vh - 80px); }
  .ksa-back-btn { display: flex; }
}
@media (min-width: 640px) {
  .ksa-back-btn { display: none; }
}
</style>
