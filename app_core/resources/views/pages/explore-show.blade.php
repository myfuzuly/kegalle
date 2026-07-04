@extends('layouts.app')
@section('title', $explore->title . ' · Explore Kegalle · Kegalle Marketplace')
@section('meta_description', $explore->description ?: 'Explore '.$explore->title.' in Kegalle district — places, activities and more.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>{{ $explore->title }}</h1><p>{{ $explore->description }}</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/explore">Explore Kegalle</a><span class="sep">›</span><span class="current">{{ $explore->title }}</span></div>

@if($explore->content)
<div class="blog-content" style="margin-top:24px;margin-bottom:32px">{!! $explore->content !!}</div>
@endif

@if($items->count())
<div class="k-gov-items-grid" style="margin-top:24px">
@foreach($items as $item)
    <div class="k-gov-item-card">
        @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" style="width:100%;height:140px;object-fit:cover;border-radius:var(--k-radius) var(--k-radius) 0 0">
        @endif
        <div class="k-gov-item-body">
            <h3>{{ $item->name }}</h3>
            @if($item->description)<p class="k-gov-item-desc">{{ $item->description }}</p>@endif
            <div class="k-gov-item-details">
                @if($item->phone)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">📞</span><span>{{ $item->phone }}</span></div>
                @endif
                @if($item->email)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">✉️</span><a href="mailto:{{ $item->email }}" style="color:var(--k-primary);text-decoration:none">{{ $item->email }}</a></div>
                @endif
                @if($item->address)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">📍</span><span>{{ $item->address }}</span></div>
                @endif
            </div>
            @if($item->map_url)
            <div style="margin-top:12px;border-radius:var(--k-radius);overflow:hidden">
                <iframe src="{{ $item->map_url }}" width="100%" height="150" style="border:0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </div>
    </div>
@endforeach
</div>
@endif

<div style="margin-top:24px;text-align:center">
    <a href="/explore" style="font-size:14px;color:var(--k-primary);font-weight:600;text-decoration:none">← Back to Explore Kegalle</a>
</div>
</div>
@endsection
