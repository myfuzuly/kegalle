@extends('layouts.dashboard')

@push('styles')
<link rel="stylesheet" href="/css/kurulla-main.css?v=16">
@endpush

@section('title','My Favorites')
@section('eyebrow','Account')
@section('heading','My Favorites')
@section('subheading','Listings you have saved for later.')

@section('content')
<section class="kd-card">
    @if($favorites->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px">
            @foreach($favorites as $favorite)
                @if($favorite->listing)
                    @include('frontend.listings.card', ['listing' => $favorite->listing])
                @endif
            @endforeach
        </div>
        <div style="margin-top:20px">{{ $favorites->links() }}</div>
    @else
        <div class="kd-empty">
            <strong>No favorites yet</strong>
            <p>Tap the heart icon on any listing to save it here.</p>
            <a href="/listings" class="kd-btn kd-btn-primary">Browse Listings</a>
        </div>
    @endif
</section>
@endsection
