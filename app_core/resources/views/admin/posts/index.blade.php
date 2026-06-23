@extends('layouts.admin')
@section('title','Blog Posts')
@section('page','Blog')
@section('heading','Blog Management')
@section('subheading','Create and manage articles shown on the public blog')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Add Article</h2><span>Full CRUD</span></div>
<form class="sa-form ka-category-form" id="createPostForm" method="post" action="/admin/posts" enctype="multipart/form-data" style="flex-wrap:wrap">@csrf
<input name="title" placeholder="Article title" required>
<input name="slug" placeholder="Slug URL optional">
<input name="excerpt" placeholder="Short excerpt">
<input type="file" name="image" accept="image/*">
<label class="ka-check"><input type="checkbox" name="is_published" value="1" checked> Published</label>
<div style="width:100%">
    <div id="createPostEditor" style="background:#fff;min-height:200px"></div>
    <textarea name="body" id="createPostBody" style="display:none"></textarea>
</div>
<button>Add Article</button>
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
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Title</th><th>Slug</th><th>Published</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($posts as $post)<tr><td><b>{{ $post->title }}</b><small>#{{ $post->id }}</small></td><td>{{ $post->slug }}</td><td>{{ optional($post->published_at)->format('M d, Y') ?? '—' }}</td><td><span class="sa-status {{ $post->is_published ? 'active' : 'suspended' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span></td><td class="sa-actions-inline"><a href="/admin/posts/{{ $post->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/posts/{{ $post->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form><form method="post" action="/admin/posts/{{ $post->id }}" onsubmit="return confirm('Delete this article?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form></td></tr>@empty<tr><td colspan="5">No articles found.</td></tr>@endforelse
</tbody></table></div>{{ $posts->links() }}</section>
@endsection
