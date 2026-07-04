@extends('layouts.app')

@php
    $gradients = ['linear-gradient(135deg,#1565C0,#0D47A1)','linear-gradient(135deg,#6A1B9A,#4A148C)','linear-gradient(135deg,#E65100,#BF360C)'];
    $postDesc = $post->meta_description ?: ($post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->body ?? ''), 155));
    $postSeoTitle = $post->meta_title ?: (($post->title ?? 'Article').' · Kegalle Marketplace Blog');
    $postImage = $post->image ? asset('storage/'.ltrim($post->image,'/')) : null;
@endphp

@section('title', $postSeoTitle)
@section('meta_description', $postDesc)
@if($postImage)
@section('og_image', $postImage)
@endif

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $postDesc,
    'image' => $postImage ?: asset('images/kegalle-placeholder.png'),
    'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at?->toIso8601String(),
    'dateModified' => $post->updated_at?->toIso8601String(),
    'author' => ['@type' => 'Organization', 'name' => 'Kegalle Marketplace'],
    'publisher' => ['@type' => 'Organization', 'name' => 'Kegalle Marketplace', 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/kegalle-placeholder.png')]],
    'url' => url('/blog/'.$post->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="blog-detail-hero">
    <div class="blog-detail-hero-inner">
        <h1 class="blog-detail-title">{{ $post->title }}</h1>
        <div class="blog-detail-meta">
            <div class="blog-detail-meta-item">
                <div class="k-author-avatar">K</div>
                <span>Kegalle Team</span>
            </div>
            <div class="blog-detail-meta-item">🗓 {{ optional($post->published_at)->format('M d, Y') }}</div>
        </div>
    </div>
</div>

<div class="container k-content-section">
    <div class="k-layout-sidebar-right k-layout-gap">
        <!-- Main content -->
        <div>
            @if($post->image)
                <div class="blog-detail-cover k-blog-cover" style="background:url('{{ asset('storage/'.ltrim($post->image,'/')) }}') center/cover"></div>
            @else
                <div class="blog-detail-cover k-blog-cover">📰</div>
            @endif

            <div class="blog-content k-blog-body">
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
                    <div class="mb-0">
                        <h2 class="k-related-title">Related Articles</h2>
                        <div class="k-grid-3">
                            @foreach($related as $item)
                                @php $g = $gradients[$loop->index % count($gradients)]; @endphp
                                <a href="/blog/{{ $item->slug }}" class="blog-grid-card k-card-link">
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
