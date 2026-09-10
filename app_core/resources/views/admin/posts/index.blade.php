@extends('layouts.admin')
@section('title','Blog Posts')
@section('page','Blog')
@section('heading','Blog Management')
@section('subheading','Create and manage articles shown on the public blog')
@section('actions')<a href="#add-article" class="ka-btn ka-btn-primary" data-scroll-to="add-article">+ Add Article</a>@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

{{-- Articles table --}}
<div class="pst-card">
    <div class="pst-card-head">
        <div class="pst-card-icon purple">✍️</div>
        <span class="pst-card-title">Articles</span>
        <span class="pst-card-meta">{{ $posts->total() }} articles</span>
    </div>
    <div class="pst-table-wrap">
    <table class="pst-table">
    <thead><tr>
        <th class="w-240">Title</th>
        <th>Excerpt</th>
        <th class="w-110">Published</th>
        <th class="w90-center">Status</th>
        <th>Actions</th>
    </tr></thead>
    <tbody>
    @forelse($posts as $post)
    <tr>
        <td>
            <span class="fw7-trunc-230">{{ $post->title }}</span>
            <small class="text-muted">#{{ $post->id }}</small>
        </td>
        <td class="fs-12h text-gray">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->body ?? ''),80) }}</td>
        <td class="fs125-nw-slate">{{ optional($post->published_at)->format('M d, Y') ?? '—' }}</td>
        <td class="text-center"><span class="sa-status {{ $post->is_published ? 'active':'suspended' }}">{{ $post->is_published ? 'Published':'Draft' }}</span></td>
        <td>
            <div class="pst-actions">
                <a href="/blog/{{ $post->slug }}" target="_blank" class="pst-act">👁 View</a>
                <a href="/admin/posts/{{ $post->id }}/edit" class="pst-act">✎ Edit</a>
                <form method="post" action="/admin/posts/{{ $post->id }}/toggle" class="d-contents">@csrf
                    <button class="pst-act">{{ $post->is_published ? 'Unpublish':'Publish' }}</button>
                </form>
                <form method="post" action="/admin/posts/{{ $post->id }}" onsubmit="return confirm('Delete this article?')" class="d-contents">@csrf @method('DELETE')
                    <button class="pst-act danger">🗑</button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td class="empty-state-lg" colspan="5">No articles yet. Add one below.</td></tr>
    @endforelse
    </tbody>
    </table>
    </div>
    <div class="p14-22">{{ $posts->links('vendor.pagination.ka-admin') }}</div>
</div>

{{-- Add Article form --}}
<div class="pst-card" id="add-article">
    <div class="pst-card-head">
        <div class="pst-card-icon green">✚</div>
        <span class="pst-card-title">Add New Article</span>
    </div>
    <form class="pst-body" id="createPostForm" method="post" action="/admin/posts" enctype="multipart/form-data">
    @csrf
    <div class="pst-grid">
        <div class="pst-field pst-full">
            <label class="pst-label">Article Title</label>
            <input name="title" class="pst-input" placeholder="e.g. Top 10 Places to Visit in Kegalle" required>
        </div>
        <div class="pst-field">
            <label class="pst-label">Slug URL</label>
            <input name="slug" class="pst-input" placeholder="auto-generated if empty">
        </div>
        <div class="pst-field">
            <label class="pst-label">Cover Image</label>
            <input type="file" name="image" class="pst-input p8-13" accept="image/*">
        </div>
        <div class="pst-field pst-full">
            <label class="pst-label">Excerpt</label>
            <input name="excerpt" class="pst-input" placeholder="Short summary shown on blog listing cards">
        </div>
        <div class="pst-field pst-full">
            <label class="pst-label mb-4px">Article Body</label>
            <div class="bg-white-220h" id="createPostEditor"></div>
            <textarea name="body" id="createPostBody" class="hidden"></textarea>
        </div>
    </div>

    <div class="pst-seo-head">SEO Settings <small class="fw4-muted">(optional — falls back to title/excerpt)</small></div>
    <div class="pst-grid">
        <div class="pst-field pst-full">
            <label class="pst-label">SEO Meta Title</label>
            <input name="meta_title" class="pst-input" placeholder="~50-60 characters" maxlength="180">
        </div>
        <div class="pst-field pst-full">
            <label class="pst-label">SEO Meta Description</label>
            <textarea name="meta_description" class="pst-textarea" rows="2" placeholder="~150-160 characters" maxlength="320"></textarea>
        </div>
    </div>
    </form>
    <div class="pst-foot">
        <button type="submit" form="createPostForm" class="pst-btn-primary">Publish Article</button>
        <label class="pst-check">
            <input type="checkbox" name="is_published" value="1" checked form="createPostForm">
            Published immediately
        </label>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    // Delegated scroll-to handler
    document.addEventListener('click',function(e){
        var el=e.target.closest('[data-scroll-to]');
        if(!el) return;
        e.preventDefault();
        var target=document.getElementById(el.dataset.scrollTo);
        if(target) target.scrollIntoView({behavior:'smooth'});
    });

    var createQuill = new Quill('#createPostEditor', { theme:'snow', placeholder:'Write the article body here…' });
    document.getElementById('createPostForm').addEventListener('submit', function(){
        document.getElementById('createPostBody').value = createQuill.root.innerHTML;
    });
})();
</script>
@endpush
