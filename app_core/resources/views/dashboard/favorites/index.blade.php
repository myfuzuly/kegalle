@extends('layouts.dashboard')
@section('banner_sub', 'Listings you\'ve saved for later.')


@section('title','My Favorites')
@section('eyebrow','Account')
@section('heading','My Favorites')
@section('subheading','Listings you have saved for later.')

@section('content')
<section class="kd-card">
    @if($favorites->count())
        <div class="grid-auto-220">
            @foreach($favorites as $favorite)
                @if($favorite->listing)
                    @include('frontend.listings.card', ['listing' => $favorite->listing])
                @endif
            @endforeach
        </div>
        <div class="mt-20">{{ $favorites->links() }}</div>
    @else
        <div class="kd-empty">
            <strong>No favorites yet</strong>
            <p>Tap the heart icon on any listing to save it here.</p>
            <a href="/listings" class="kd-btn kd-btn-primary">Browse Listings</a>
        </div>
    @endif
</section>
@endsection
