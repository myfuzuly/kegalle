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

<section class="blog-hero">
    <div class="blog-hero-inner">
        <h1>Kegalle Marketplace Blog</h1>
        <p>Tips, guides and local insights to help you buy, sell and discover more.</p>
        <form class="blog-hero-search" action="/blog" method="GET">
            <input type="text" name="q" placeholder="Search articles, tips, guides...">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<div class="container k-content-section">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Blog</span></div>

    <div class="k-layout-sidebar-right k-layout-gap">
        <!-- Main content -->
        <div>
            @if($featured)
                <div class="k-section">
                    <div class="k-section-header"><h2 class="k-section-title">Featured Article</h2></div>
                    <a href="/blog/{{ $featured->slug }}" class="blog-featured-card k-card-link">
                        @if($featured->image)
                            <div class="blog-featured-img" style="background-image:url('{{ asset('storage/'.ltrim($featured->image,'/')) }}');background-size:cover;background-position:center"></div>
                        @else
                            <div class="blog-featured-img">📰</div>
                        @endif
                        <div class="blog-featured-body">
                            <div class="blog-featured-title">{{ $featured->title }}</div>
                            <div class="blog-featured-excerpt">{{ $featured->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($featured->body ?? ''), 220) }}</div>
                            <div class="k-blog-meta-row">
                                <div class="blog-author">
                                    <div class="blog-author-avatar">K</div>
                                    <div>
                                        <div class="blog-author-name">Kegalle Team</div>
                                        <div class="blog-author-date">{{ optional($featured->published_at)->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <span class="k-btn k-btn-primary k-btn-sm mt-14">Read Article →</span>
                        </div>
                    </a>
                </div>
            @endif

            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Latest Articles</h2>
                    <span class="k-text-tertiary k-text-sm">{{ $posts->total() }} articles</span>
                </div>
                @if($rest->count())
                    <div class="k-grid-3">
                        @foreach($rest as $post)
                            @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                            <a href="/blog/{{ $post->slug }}" class="blog-grid-card k-card-link">
                                @if($post->image)
                                    <div class="blog-grid-img" style="background-image:url('{{ asset('storage/'.ltrim($post->image,'/')) }}');background-size:cover;background-position:center"></div>
                                @else
                                    <div class="blog-grid-img" style="background:{{ $g }}">📰</div>
                                @endif
                                <div class="blog-grid-body">
                                    <div class="blog-grid-title">{{ $post->title }}</div>
                                    <div class="blog-grid-excerpt">{{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->body ?? ''), 110) }}</div>
                                    <div class="blog-grid-footer">
                                        <span class="k-text-muted k-text-xs">{{ optional($post->published_at)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @elseif(!$featured)
                    <div class="k-blog-empty-state">
                        <h3 class="k-blog-empty-title">No articles yet</h3>
                        <p class="k-text-secondary">Check back soon for tips, guides and local insights.</p>
                    </div>
                @endif
            </div>

            {{ $posts->links('vendor.pagination.k-theme') }}
        </div>

        <!-- Sidebar -->
        <div>
            @if($popular->count())
                <div class="k-card k-card-body mb-16 k-blog-sidebar-card">
                    <h3 class="k-sidebar-heading">Popular Articles</h3>
                    <div>
                        @foreach($popular as $pop)
                            @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                            <a href="/blog/{{ $pop->slug }}" class="blog-popular-card k-card-link">
                                <div class="blog-popular-img" style="background:{{ $g }}">📰</div>
                                <div><div class="blog-popular-title">{{ $pop->title }}</div><div class="blog-popular-date">{{ optional($pop->published_at)->format('M d, Y') }}</div></div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-16">
                @include('frontend.partials.ad-banner', ['location' => 'blog_sidebar', 'style' => 'box'])
            </div>

            <div class="k-newsletter-cta">
                <div class="k-newsletter-icon">📬</div>
                <h3 class="k-newsletter-title">Stay Updated</h3>
                <p class="k-newsletter-desc">Get the latest articles and marketplace updates in your inbox.</p>
                <form method="POST" action="#">
                    @csrf
                    <input type="email" name="email" placeholder="Your email address" class="k-newsletter-input">
                    <button type="submit" class="k-btn k-btn-primary w-full k-btn-center k-newsletter-btn">Subscribe Free</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
