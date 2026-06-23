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

<div class="container" style="padding-top:32px;padding-bottom:48px">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Blog</span></div>

    <div class="k-layout-sidebar-right" style="gap:32px">
        <!-- Main content -->
        <div>
            @if($featured)
                <div class="k-section">
                    <div class="k-section-header"><h2 class="k-section-title">Featured Article</h2></div>
                    <a href="/blog/{{ $featured->slug }}" class="blog-featured-card" style="text-decoration:none;color:inherit">
                        @if($featured->image)
                            <div class="blog-featured-img" style="background-image:url('{{ asset('storage/'.ltrim($featured->image,'/')) }}');background-size:cover;background-position:center"></div>
                        @else
                            <div class="blog-featured-img">📰</div>
                        @endif
                        <div class="blog-featured-body">
                            <div class="blog-featured-title">{{ $featured->title }}</div>
                            <div class="blog-featured-excerpt">{{ $featured->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($featured->body ?? ''), 220) }}</div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px">
                                <div class="blog-author">
                                    <div class="blog-author-avatar">K</div>
                                    <div>
                                        <div class="blog-author-name">Kegalle Team</div>
                                        <div class="blog-author-date">{{ optional($featured->published_at)->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            <span class="k-btn k-btn-primary k-btn-sm" style="margin-top:14px">Read Article →</span>
                        </div>
                    </a>
                </div>
            @endif

            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Latest Articles</h2>
                    <span style="font-size:13px;color:var(--k-text-tertiary)">{{ $posts->total() }} articles</span>
                </div>
                @if($rest->count())
                    <div class="k-grid-3">
                        @foreach($rest as $post)
                            @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                            <a href="/blog/{{ $post->slug }}" class="blog-grid-card" style="text-decoration:none;color:inherit">
                                @if($post->image)
                                    <div class="blog-grid-img" style="background-image:url('{{ asset('storage/'.ltrim($post->image,'/')) }}');background-size:cover;background-position:center"></div>
                                @else
                                    <div class="blog-grid-img" style="background:{{ $g }}">📰</div>
                                @endif
                                <div class="blog-grid-body">
                                    <div class="blog-grid-title">{{ $post->title }}</div>
                                    <div class="blog-grid-excerpt">{{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->body ?? ''), 110) }}</div>
                                    <div class="blog-grid-footer">
                                        <span style="font-size:11px;color:var(--k-text-muted)">{{ optional($post->published_at)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @elseif(!$featured)
                    <div style="padding:60px 20px;text-align:center;background:var(--k-surface);border:1px dashed var(--k-border);border-radius:var(--k-radius-lg)">
                        <h3 style="font-family:var(--font-display);margin-bottom:8px">No articles yet</h3>
                        <p style="color:var(--k-text-secondary)">Check back soon for tips, guides and local insights.</p>
                    </div>
                @endif
            </div>

            {{ $posts->links('vendor.pagination.k-theme') }}
        </div>

        <!-- Sidebar -->
        <div>
            @if($popular->count())
                <div class="k-card k-card-body mb-16" style="border-radius:var(--k-radius-lg)">
                    <h3 style="font-family:var(--font-display);font-size:14px;font-weight:700;margin-bottom:14px">Popular Articles</h3>
                    <div>
                        @foreach($popular as $pop)
                            @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                            <a href="/blog/{{ $pop->slug }}" class="blog-popular-card" style="text-decoration:none;color:inherit">
                                <div class="blog-popular-img" style="background:{{ $g }}">📰</div>
                                <div><div class="blog-popular-title">{{ $pop->title }}</div><div class="blog-popular-date">{{ optional($pop->published_at)->format('M d, Y') }}</div></div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="background:linear-gradient(135deg,#1B5E20,#0D1B2A);border-radius:var(--k-radius-xl);padding:24px;color:#fff">
                <div style="font-size:28px;margin-bottom:10px">📬</div>
                <h3 style="font-family:var(--font-display);font-size:16px;font-weight:800;margin-bottom:6px">Stay Updated</h3>
                <p style="font-size:13px;color:rgba(255,255,255,.75);margin-bottom:16px">Get the latest articles and marketplace updates in your inbox.</p>
                <form method="POST" action="#">
                    @csrf
                    <input type="email" name="email" placeholder="Your email address" style="width:100%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:#fff;border-radius:var(--k-radius);padding:10px 14px;font-size:13px;outline:none;margin-bottom:10px;font-family:var(--font-body)">
                    <button type="submit" class="k-btn k-btn-primary w-full" style="justify-content:center;border:none;cursor:pointer">Subscribe Free</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
