@extends('layouts.app')

@section('title','Kegalle Marketplace Blog — Tips, Guides & Local Insights')
@section('meta_description','Tips, guides and local insights for buyers and sellers in Kegalle — travel guides, safety tips, and advice to help you buy and sell smarter.')
@section('canonical', url('/blog'))

@section('content')
@php
    $gradients = ['linear-gradient(135deg,#1565C0,#0D47A1)','linear-gradient(135deg,#6A1B9A,#4A148C)','linear-gradient(135deg,#E65100,#BF360C)','linear-gradient(135deg,#2E7D32,#1B5E20)','linear-gradient(135deg,#1565C0,#1B5E20)','linear-gradient(135deg,#F9A825,#E65100)'];
    $featured = $posts->first();
    $rest = $featured ? $posts->skip(1) : collect();
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────────────────── --}}
<div class="bl-hero">
    <div class="bl-hero-overlay"></div>
    <div class="bl-hero-inner">
        <div class="bl-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Kegalle Marketplace
        </div>
        <h1 class="bl-hero-title">The Kegalle Blog</h1>
        <p class="bl-hero-sub">Tips, guides and local insights to help you buy, sell and discover more in Kegalle.</p>
        <form class="bl-hero-search" action="/blog" method="GET">
            <input type="text" name="q" placeholder="Search articles, tips, guides..." value="{{ request('q') }}" class="bl-hero-input">
            <button type="submit" class="bl-hero-search-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Search
            </button>
        </form>
    </div>
</div>

{{-- ── Stats bar ─────────────────────────────────────────────────────────────── --}}
<div class="bl-stats-bar">
    <div class="bl-stat"><span class="bl-stat-n">{{ $posts->total() }}</span><span class="bl-stat-l">Articles</span></div>
    <div class="bl-stat-div"></div>
    <div class="bl-stat"><span class="bl-stat-n">Free</span><span class="bl-stat-l">To Read</span></div>
    <div class="bl-stat-div"></div>
    <div class="bl-stat"><span class="bl-stat-n">Local</span><span class="bl-stat-l">Insights</span></div>
    <div class="bl-stat-div"></div>
    <div class="bl-stat"><span class="bl-stat-n">Weekly</span><span class="bl-stat-l">Updates</span></div>
</div>

{{-- ── Content ───────────────────────────────────────────────────────────────── --}}
<div class="bl-wrap">
    <div class="bl-breadcrumb"><a href="/">Home</a><span>›</span><span>Blog</span></div>

    <div class="bl-layout">
        {{-- Main column --}}
        <div class="bl-main">
            @if($featured)
            <div class="bl-section">
                <div class="bl-section-head">
                    <h2 class="bl-section-title">Featured Article</h2>
                </div>
                <a href="/blog/{{ $featured->slug }}" class="bl-featured">
                    @if($featured->image)
                        <div class="bl-featured-img" style="background-image:url('{{ asset('storage/'.ltrim($featured->image,'/')) }}')"></div>
                    @else
                        <div class="bl-featured-img bl-featured-img--fallback" style="background:linear-gradient(135deg,#1b5e20,#2e7d32)">
                            <span class="bl-featured-img-title">{{ Str::limit($featured->title, 60) }}</span>
                        </div>
                    @endif
                    <div class="bl-featured-body">
                        <h3 class="bl-featured-title">{{ $featured->title }}</h3>
                        <p class="bl-featured-excerpt">{{ $featured->excerpt ?? Str::limit(strip_tags($featured->body ?? ''), 200) }}</p>
                        <div class="bl-featured-meta">
                            <div class="bl-author">
                                <div class="bl-author-av">K</div>
                                <div>
                                    <div class="bl-author-name">Kegalle Team</div>
                                    <div class="bl-author-date">{{ optional($featured->published_at)->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <span class="bl-read-btn">Read Article <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg></span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <div class="bl-section">
                <div class="bl-section-head">
                    <h2 class="bl-section-title">Latest Articles</h2>
                    <span class="bl-section-count">{{ $posts->total() }} articles</span>
                </div>
                @if($rest->count())
                    <div class="bl-grid">
                        @foreach($rest as $post)
                            @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                            <a href="/blog/{{ $post->slug }}" class="bl-card">
                                @if($post->image)
                                    <div class="bl-card-img" style="background-image:url('{{ asset('storage/'.ltrim($post->image,'/')) }}')"></div>
                                @else
                                    <div class="bl-card-img bl-card-img--fallback" style="background:{{ $g }}">
                                        <span>{{ Str::limit($post->title, 48) }}</span>
                                    </div>
                                @endif
                                <div class="bl-card-body">
                                    <h3 class="bl-card-title">{{ $post->title }}</h3>
                                    <p class="bl-card-excerpt">{{ $post->excerpt ?? Str::limit(strip_tags($post->body ?? ''), 100) }}</p>
                                    <div class="bl-card-footer">
                                        <span class="bl-card-date">{{ optional($post->published_at)->format('M d, Y') }}</span>
                                        <span class="bl-card-cta">Read <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg></span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @elseif(!$featured)
                    <div class="bl-empty">
                        <div class="bl-empty-icon">📝</div>
                        <h3 class="bl-empty-title">No articles yet</h3>
                        <p class="bl-empty-sub">Check back soon for tips, guides and local insights.</p>
                    </div>
                @endif
            </div>

            <div class="bl-pagination">
                {{ $posts->links('vendor.pagination.k-theme') }}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="bl-sidebar">
            @if($popular->count() && $posts->total() >= 4)
            <div class="bl-sidebar-card">
                <h3 class="bl-sidebar-title">Popular Articles</h3>
                @foreach($popular as $pop)
                    @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                    <a href="/blog/{{ $pop->slug }}" class="bl-popular">
                        <div class="bl-popular-img" style="background:{{ $g }}">📰</div>
                        <div class="bl-popular-body">
                            <div class="bl-popular-title">{{ $pop->title }}</div>
                            <div class="bl-popular-date">{{ optional($pop->published_at)->format('M d, Y') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
            @endif

            <div class="bl-sidebar-ad">
                @include('frontend.partials.ad-banner', ['location' => 'blog_sidebar', 'style' => 'box'])
            </div>
        </div>
    </div>
</div>

@endsection
