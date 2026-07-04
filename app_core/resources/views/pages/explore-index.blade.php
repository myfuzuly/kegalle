@extends('layouts.app')
@section('title','Explore Kegalle — Culture, Nature, History & More · Kegalle Marketplace')
@section('meta_description','Discover the best of Kegalle district — activities, tourist attractions, natural resources and historic places, all in one place.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Explore Kegalle</h1><p>Discover the best of Kegalle – culture, nature, history and more.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Explore Kegalle</span></div>

<div class="k-gov-grid" style="margin-top:24px">
@forelse($exploreItems as $explore)
    <a href="/explore/{{ $explore->slug }}" class="k-gov-card" style="text-decoration:none">
        <div class="k-gov-icon" style="background:{{ $explore->image ? 'center/cover no-repeat url(\''.asset('storage/'.$explore->image).'\')' : 'linear-gradient(135deg,'.$explore->gradient_start.','.$explore->gradient_end.')' }}">
            @unless($explore->image)<span style="font-size:32px">{{ $explore->icon }}</span>@endunless
        </div>
        <h3>{{ $explore->title }}</h3>
        <p>{{ $explore->description ?: \Illuminate\Support\Str::limit($explore->items, 80) }}</p>
        <span class="k-gov-arrow">→</span>
    </a>
@empty
    <p style="padding:30px;text-align:center;color:var(--k-text-tertiary)">No explore content yet — check back soon.</p>
@endforelse
</div>
</div>
@endsection
