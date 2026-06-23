@extends('layouts.app')

@php
    $gradients = ['linear-gradient(135deg,#1565C0,#0D47A1)','linear-gradient(135deg,#6A1B9A,#4A148C)','linear-gradient(135deg,#E65100,#BF360C)'];
    $postDesc = $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->body ?? ''), 155);
    $postImage = $post->image ? asset('storage/'.ltrim($post->image,'/')) : null;
@endphp

@section('title', ($post->title ?? 'Article').' · Kegalle Marketplace Blog')
@section('meta_description', $postDesc)
@if($postImage)
@section('og_image', $postImage)
@endif

@section('content')

<div class="blog-detail-hero">
    <div class="blog-detail-hero-inner">
        <h1 class="blog-detail-title">{{ $post->title }}</h1>
        <div class="blog-detail-meta">
            <div class="blog-detail-meta-item">
                <div style="width:30px;height:30px;border-radius:50%;background:var(--k-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px">K</div>
                <span>Kegalle Team</span>
            </div>
            <div class="blog-detail-meta-item">🗓 {{ optional($post->published_at)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:48px">
    <div class="k-layout-sidebar-right" style="gap:32px">
        <!-- Main content -->
        <div>
            @if($post->image)
                <div class="blog-detail-cover" style="background:url('{{ asset('storage/'.ltrim($post->image,'/')) }}') center/cover;margin:0 0 28px"></div>
            @else
                <div class="blog-detail-cover" style="margin:0 0 28px">📰</div>
            @endif

            <div class="blog-content" style="max-width:none;padding:0">
                @if($post->body)
                    {!! $post->body !!}
                @else
                    <p>{{ $post->excerpt }}</p>
                @endif

                <div class="blog-share">
                    <span class="blog-share-label">Share this article</span>
                    <div class="blog-share-btns">
                        <button class="blog-share-btn" title="Share on Facebook" aria-label="Share on Facebook">f</button>
                        <button class="blog-share-btn" title="Share on Twitter/X" aria-label="Share on Twitter/X">𝕏</button>
                        <button class="blog-share-btn" title="Share on WhatsApp" aria-label="Share on WhatsApp">💬</button>
                        <button class="blog-share-btn" title="Copy link" aria-label="Copy link" onclick="navigator.clipboard.writeText(window.location.href)">🔗</button>
                    </div>
                </div>

                <div class="blog-author-card">
                    <div class="blog-author-card-avatar">K</div>
                    <div>
                        <div class="blog-author-card-name">Kegalle Editorial Team</div>
                        <div class="blog-author-card-role">Local Content & Marketplace Team</div>
                        <div class="blog-author-card-bio">We write guides, tips and local insights to help buyers, sellers and visitors get the most out of Kegalle Marketplace and the Kegalle district.</div>
                    </div>
                </div>

                @if($related->count())
                    <div style="margin-bottom:0">
                        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;margin-bottom:20px;padding-bottom:0;border-bottom:none">Related Articles</h2>
                        <div class="k-grid-3">
                            @foreach($related as $item)
                                @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                                <a href="/blog/{{ $item->slug }}" class="blog-grid-card" style="text-decoration:none;color:inherit">
                                    <div class="blog-grid-img" style="background:{{ $g }}">📰</div>
                                    <div class="blog-grid-body">
                                        <div class="blog-grid-title">{{ $item->title }}</div>
                                        <div class="blog-grid-footer"><span class="text-muted text-xs">{{ optional($item->published_at)->format('M d, Y') }}</span></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar (same as blog list page) -->
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

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": {!! json_encode($post->title) !!},
    "description": {!! json_encode($postDesc) !!},
    @if($postImage)
    "image": {!! json_encode($postImage) !!},
    @endif
    "datePublished": {!! json_encode(optional($post->published_at)->toIso8601String()) !!},
    "author": {"@type": "Organization", "name": "Kegalle Marketplace"},
    "publisher": {"@type": "Organization", "name": "Kegalle Marketplace", "logo": {"@type": "ImageObject", "url": {!! json_encode(asset('images/kegalle-placeholder.png')) !!}}},
    "mainEntityOfPage": {!! json_encode(url()->current()) !!}
}
</script>
@endpush
@endsection
