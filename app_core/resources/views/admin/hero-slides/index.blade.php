@extends('layouts.admin')
@section('title','Hero Slides')

@push('styles')

@endpush

@section('content')
<div class="alc-page-header">
    <div>
        <h1 class="alc-page-title">Hero Slides</h1>
        <p class="alc-page-sub">Manage the background images shown in the homepage hero section.</p>
    </div>
</div>

@if(session('success'))
<div class="hs-alert hs-alert-ok">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="hs-alert hs-alert-err">{{ $errors->first() }}</div>
@endif

<div class="grid-340-start">

    {{-- Slide list --}}
    <div>
        @if($slides->isEmpty())
        <div class="hs-empty">No slides yet. Add one on the right.</div>
        @else
        <div class="hs-grid">
            @foreach($slides as $slide)
            <div class="hs-card">
                <div class="hs-card-img">
                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title ?? 'Slide' }}" loading="lazy">
                    <span class="hs-card-badge {{ $slide->is_active ? 'hs-badge-active' : 'hs-badge-hidden' }}">
                        {{ $slide->is_active ? 'Active' : 'Hidden' }}
                    </span>
                </div>
                <div class="hs-card-body">
                    <div class="hs-card-title">{{ $slide->title ?: '(no title)' }}</div>
                    <div class="hs-card-order">Order: {{ $slide->sort_order }}</div>
                    <div class="hs-card-actions">
                        <form method="POST" action="/admin/hero-slides/{{ $slide->id }}/toggle">
                            @csrf @method('POST')
                            <button type="submit" class="hs-btn hs-btn-toggle">
                                {{ $slide->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <form method="POST" action="/admin/hero-slides/{{ $slide->id }}" class="hs-del-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="hs-btn hs-btn-del">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Add slide --}}
    <div class="hs-add-card">
        <h3>Add New Slide</h3>
        <form method="POST" action="/admin/hero-slides" enctype="multipart/form-data">
            @csrf
            <div class="hs-field">
                <label for="hs_image">Image <span class="text-danger">*</span></label>
                <label class="hs-file-label" for="hs_image">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span id="hs_file_name">Choose image (JPG, PNG, WebP · max 10MB)</span>
                    <input type="file" id="hs_image" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" required>
                </label>
            </div>
            <div class="hs-field">
                <label for="hs_title">Title / Alt text</label>
                <input type="text" id="hs_title" name="title" placeholder="e.g. Kegalle Clock Tower" value="{{ old('title') }}">
            </div>
            <div class="hs-field">
                <label for="hs_order">Sort order</label>
                <input type="number" id="hs_order" name="sort_order" value="{{ old('sort_order', $slides->count() + 1) }}" min="0">
            </div>
            <div class="hs-toggle-row">
                <input type="checkbox" id="hs_active" name="is_active" value="1" class="hs-check" checked>
                <label class="csp5-236" for="hs_active">Active (visible on site)</label>
            </div>
            <button type="submit" class="hs-submit mt-18">Upload Slide</button>
        </form>
    </div>

</div>

<script nonce="{{ $cspNonce ?? '' }}">
document.getElementById('hs_image').addEventListener('change', function() {
    var label = document.getElementById('hs_file_name');
    label.textContent = this.files[0] ? this.files[0].name : 'Choose image';
});
document.querySelectorAll('.hs-del-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm('Delete this slide?')) e.preventDefault();
    });
});
</script>
@endsection
