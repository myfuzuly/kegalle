<template>
  <div class="kdl-shell">
    <Head title="Dashboard · Kegalle" />

    <!-- Sidebar (static for now — will be a component in Phase 2.1) -->
    <aside class="kdl-sidebar" id="kdlSidebar">
      <div class="kdl-sidebar-header">
        <a href="/" class="kdl-brand">
          <img :src="'/images/kegalle-logo.png'" alt="Kegalle" class="kdl-brand-img" width="130" height="44">
        </a>
        <button type="button" class="kdl-sidebar-close" id="kdlSidebarClose" @click="closeSidebar" aria-label="Close menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <nav class="kdl-nav">
        <a href="/dashboard" class="ksp-item ksp-item-active">
          <span class="ksp-ic"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span>
          Overview
        </a>
        <a href="/dashboard/listings" class="ksp-item">
          <span class="ksp-ic"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
          My Listings
        </a>
        <a href="/dashboard/stores" class="ksp-item">
          <span class="ksp-ic"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
          My Stores
        </a>
        <a href="/dashboard/chat" class="ksp-item">
          <span class="ksp-ic"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
          Chats
        </a>
        <a href="/dashboard/profile" class="ksp-item">
          <span class="ksp-ic"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
          Profile
        </a>
      </nav>
    </aside>

    <div class="kdl-sidebar-overlay" :class="{ active: sidebarOpen }" @click="closeSidebar"></div>

    <!-- Main area -->
    <div class="kdl-main">
      <header class="kdl-topbar">
        <div class="kdl-topbar-left">
          <button type="button" class="kdl-hamburger" @click="openSidebar" aria-label="Open menu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <div>
            <div class="kdl-breadcrumb"><span>kegalle</span> <span>/</span> <span>Dashboard</span></div>
            <div class="kdl-topbar-title">Overview</div>
          </div>
        </div>
        <div class="kdl-topbar-right">
          <a href="/dashboard/notifications" class="kdl-tb-btn-icon" title="Notifications">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
          </a>
          <div class="kdl-userchip">
            <div class="kdl-userchip-av">{{ userInitial }}</div>
            <span class="kdl-userchip-name">{{ auth.user?.name }}</span>
          </div>
          <form method="POST" action="/logout" style="margin:0">
            <input type="hidden" name="_token" :value="$page.props.csrfToken">
            <button type="submit" class="kdl-tb-btn kdl-tb-btn-logout">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
              <span class="kdl-signout-label">Sign Out</span>
            </button>
          </form>
        </div>
      </header>

      <main class="kdl-content">

        <!-- Flash message -->
        <div v-if="flash.success" class="dbi-flash-success">{{ flash.success }}</div>

        <!-- Profile incomplete banner -->
        <div v-if="profileIncomplete" class="dbi-profile-remind" id="dbiProfileRemind">
          <div class="dbi-pr-body">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Complete your profile to get better visibility — add a photo, phone number and location.</span>
            <a href="/dashboard/profile" class="dbi-pr-link">Complete Profile →</a>
          </div>
          <button type="button" class="dbi-pr-close" @click="dismissProfileRemind" aria-label="Dismiss">×</button>
        </div>

        <!-- Onboarding progress -->
        <div v-if="showOnboarding" class="dbi-onboard">
          <div class="dbi-ob-head">
            <div>
              <div class="dbi-ob-title">Getting started</div>
              <div class="dbi-ob-sub">{{ onboardingDone }} of {{ onboardingTotal }} steps complete</div>
            </div>
            <div class="dbi-ob-bar-wrap">
              <div class="dbi-ob-bar" :style="{ width: onboardingPct + '%' }"></div>
            </div>
          </div>
          <div class="dbi-ob-steps">
            <div v-for="(done, key) in onboarding" :key="key" class="dbi-ob-step" :class="{ done }">
              <span class="dbi-ob-check">{{ done ? '✓' : '○' }}</span>
              <span>{{ stepLabel(key) }}</span>
            </div>
          </div>
        </div>

        <!-- Stats row -->
        <div class="dbi-stats">
          <div class="dbi-stat-card">
            <div class="dbi-stat-num">{{ stats.listings }}</div>
            <div class="dbi-stat-label">Total Listings</div>
          </div>
          <div class="dbi-stat-card dbi-stat-green">
            <div class="dbi-stat-num">{{ stats.approved }}</div>
            <div class="dbi-stat-label">Live / Approved</div>
          </div>
          <div class="dbi-stat-card dbi-stat-amber">
            <div class="dbi-stat-num">{{ stats.pending }}</div>
            <div class="dbi-stat-label">Pending Review</div>
          </div>
          <div class="dbi-stat-card dbi-stat-blue">
            <div class="dbi-stat-num">{{ stats.stores }}</div>
            <div class="dbi-stat-label">Stores</div>
          </div>
        </div>

        <!-- Quick actions -->
        <div class="dbi-actions">
          <a href="/dashboard/listings/create" class="dbi-action-btn dbi-action-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Post New Ad
          </a>
          <a href="/dashboard/stores/create" class="dbi-action-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Create Store
          </a>
          <a href="/dashboard/profile" class="dbi-action-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Edit Profile
          </a>
        </div>

        <!-- Recent listings -->
        <div class="dbi-section">
          <div class="dbi-section-head">
            <h2 class="dbi-section-title">Recent Listings</h2>
            <a href="/dashboard/listings" class="dbi-section-link">View all →</a>
          </div>
          <div v-if="latestListings.length" class="dbi-listings-grid">
            <div v-for="listing in latestListings" :key="listing.id" class="dbi-listing-card">
              <div class="dbi-lc-img-wrap">
                <img
                  v-if="listing.images?.length"
                  :src="listing.images[0].url"
                  :alt="listing.title"
                  class="dbi-lc-img"
                  loading="lazy"
                >
                <div v-else class="dbi-lc-img-placeholder">📷</div>
              </div>
              <div class="dbi-lc-body">
                <div class="dbi-lc-title">{{ listing.title }}</div>
                <div class="dbi-lc-meta">
                  <span class="dbi-lc-badge" :class="'dbi-badge-' + listing.status">{{ listing.status }}</span>
                  <span v-if="listing.price" class="dbi-lc-price">LKR {{ Number(listing.price).toLocaleString() }}</span>
                </div>
                <div class="dbi-lc-actions">
                  <a :href="'/dashboard/listings/' + listing.id + '/edit'" class="dbi-lc-btn">Edit</a>
                  <a v-if="listing.status === 'approved'" :href="'/listings/' + listing.slug" target="_blank" class="dbi-lc-btn dbi-lc-btn-outline">View →</a>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="dbi-empty">
            <p>No listings yet. <a href="/dashboard/listings/create">Post your first ad →</a></p>
          </div>
        </div>

        <!-- Empty store nudge -->
        <div v-if="emptyStores.length" class="dbi-section">
          <div class="dbi-section-head">
            <h2 class="dbi-section-title">Stores needing products</h2>
          </div>
          <div class="dbi-store-nudges">
            <div v-for="store in emptyStores" :key="store.id" class="dbi-nudge-card">
              <strong>{{ store.name }}</strong> has no products yet.
              <a :href="'/dashboard/stores/' + store.id + '/products/create'" class="dbi-nudge-btn">Add Products →</a>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const auth = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)

const props = defineProps({
  stats: Object,
  latestListings: Array,
  emptyStores: Array,
  savedSearches: Array,
  onboarding: Object,
  onboardingDone: Number,
  onboardingTotal: Number,
  showOnboarding: Boolean,
  profileIncomplete: Boolean,
  isFirstLogin: Boolean,
})

const userInitial = computed(() => {
  const name = auth.value.user?.name ?? 'U'
  return name.charAt(0).toUpperCase()
})

const onboardingPct = computed(() =>
  props.onboardingTotal ? Math.round((props.onboardingDone / props.onboardingTotal) * 100) : 0
)

const stepLabels = {
  account_created: 'Account created',
  profile_complete: 'Complete your profile',
  store_created: 'Create your store',
  store_with_logo: 'Add a store logo',
  first_listing: 'Post your first listing',
  first_approved: 'Get your first listing approved',
}
const stepLabel = key => stepLabels[key] ?? key

const sidebarOpen = ref(false)
const openSidebar = () => {
  sidebarOpen.value = true
  document.getElementById('kdlSidebar')?.classList.add('open')
}
const closeSidebar = () => {
  sidebarOpen.value = false
  document.getElementById('kdlSidebar')?.classList.remove('open')
}

const profileIncompleteVisible = ref(true)
const dismissProfileRemind = () => { profileIncompleteVisible.value = false }
</script>
