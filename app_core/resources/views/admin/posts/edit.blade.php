@extends('layouts.admin')

@section('title','Edit Article')
@section('page','Blog')
@section('heading','Edit Article')
@section('subheading','Update article content, image and publish status')

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/posts">Back</a>
@endsection

@section('content')
<section class="sa-card">
    <form class="ka-premium-form" method="post" action="/admin/posts/{{ $post->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="ka-form-grid">
            <div class="ka-field">
                <label>Title</label>
                <input name="title" value="{{ old('title', $post->title) }}" required>
            </div>

            <div class="ka-field">
                <label>Slug URL</label>
                <input name="slug" value="{{ old('slug', $post->slug) }}" required>
            </div>

            <div class="ka-field">
                <label>Excerpt</label>
                <input name="excerpt" value="{{ old('excerpt', $post->excerpt) }}">
            </div>

            <div class="ka-field">
                <label>Cover Image</label>
                <input type="file" name="image" accept="image/*">
                @if($post->image)
                    <small>Current: {{ $post->image }}</small>
                @endif
            </div>

            <div class="ka-field" style="grid-column:1/-1">
                <label>Body (HTML allowed)</label>
                <div id="editPostEditor" style="background:#fff;min-height:280px"></div>
                <textarea name="body" id="editPostBody" style="display:none">{{ old('body', $post->body) }}</textarea>
            </div>

            <label class="ka-check">
                <input type="checkbox" name="is_published" value="1" @checked($post->is_published)>
                Published
            </label>
        </div>

        <div class="ka-form-actions">
            <button class="ka-btn ka-btn-primary">Update Article</button>
            <a class="ka-btn ka-btn-light" href="/admin/posts">Cancel</a>
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
    var bodyField = document.getElementById('editPostBody');
    var editQuill = new Quill('#editPostEditor', { theme: 'snow', placeholder: 'Write the article body here…' });
    editQuill.root.innerHTML = bodyField.value;
    bodyField.closest('form').addEventListener('submit', function () {
        bodyField.value = editQuill.root.innerHTML;
    });
})();
</script>
@endpush
@endsection
