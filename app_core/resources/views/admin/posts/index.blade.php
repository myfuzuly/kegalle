@extends('layouts.admin')
@section('title','Blog Posts')
@section('page','Blog')
@section('heading','Blog Management')
@section('subheading','Create and manage articles shown on the public blog')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Add Article</h2><span>Full CRUD</span></div>
<form class="ka-premium-form" id="createPostForm" method="post" action="/admin/posts" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2"><label>Article Title</label><input name="title" placeholder="e.g. Top 10 Places to Visit in Kegalle" required></div>
    <div class="ka-field"><label>Slug URL</label><input name="slug" placeholder="auto-generated if empty"></div>
    <div class="ka-field"><label>Cover Image</label><input type="file" name="image" accept="image/*"></div>
    <div class="ka-field ka-span-2"><label>Excerpt</label><input name="excerpt" placeholder="Short summary shown on blog listing cards"></div>
    <label class="ka-check"><input type="checkbox" name="is_published" value="1" checked> Published</label>
</div>

<div style="margin-top:16px">
    <label style="display:block;font-size:13px;font-weight:700;margin-bottom:8px;color:#0D1B2A">Article Body</label>
    <div id="createPostEditor" style="background:#fff;min-height:200px"></div>
    <textarea name="body" id="createPostBody" style="display:none"></textarea>
</div>

<div style="border-top:1px solid #E5E8EF;margin-top:18px;padding-top:14px">
    <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:#0D1B2A">🔍 SEO Settings (optional — falls back to title/excerpt if left blank)</div>
    <div class="ka-form-grid">
        <div class="ka-field ka-span-2"><label>SEO Meta Title</label><input name="meta_title" placeholder="~50-60 characters ideal" maxlength="180"></div>
        <div class="ka-field ka-span-2"><label>SEO Meta Description</label><textarea name="meta_description" placeholder="~150-160 characters ideal" maxlength="320"></textarea></div>
    </div>
</div>

<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Add Article</button>
</div>
</form>
</section>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
(function () {
    var createQuill = new Quill('#createPostEditor', { theme: 'snow', placeholder: 'Write the article body here…' });
    document.getElementById('createPostForm').addEventListener('submit', function () {
        document.getElementById('createPostBody').value = createQuill.root.innerHTML;
    });
})();
</script>
@endpush
<section class="sa-card"><div class="sa-card-head"><h2>Articles from Database</h2><span>{{ $posts->total() }} articles</span></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-posts"><thead><tr><th>Title</th><th>Slug</th><th>Published</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($posts as $post)<tr><td><b>{{ $post->title }}</b><small>#{{ $post->id }}</small></td><td>{{ $post->slug }}</td><td>{{ optional($post->published_at)->format('M d, Y') ?? '—' }}</td><td><span class="sa-status {{ $post->is_published ? 'active' : 'suspended' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span></td><td class="sa-actions-inline"><a href="/admin/posts/{{ $post->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/posts/{{ $post->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form><form method="post" action="/admin/posts/{{ $post->id }}" onsubmit="return confirm('Delete this article?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form></td></tr>@empty<tr><td colspan="5">No articles found.</td></tr>@endforelse
</tbody></table></div>{{ $posts->links() }}</section>
@endsection
