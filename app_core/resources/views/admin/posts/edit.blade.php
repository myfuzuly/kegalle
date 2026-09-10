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

            <div class="ka-field grid-full">
                <label>Body (HTML allowed)</label>
                <div class="bg-white-280h" id="editPostEditor"></div>
                <textarea name="body" id="editPostBody" class="hidden">{{ old('body', $post->body) }}</textarea>
            </div>

            <label class="ka-check">
                <input type="checkbox" name="is_published" value="1" @checked($post->is_published)>
                Published
            </label>
        </div>

        <div class="bt-mt18-pt16">
            <div class="fw8-fs14-mb4">🔍 SEO Settings</div>
            <p class="fs12-gray-mb14">Falls back to the article title and excerpt if left blank.</p>

            <div class="ka-field" class="mb-10">
                <label>SEO Meta Title <span class="text-light-gray" id="metaTitleCount"></span></label>
                <input id="metaTitleInput" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="180" placeholder="{{ $post->title }}">
            </div>
            <div class="ka-field" class="mb-14">
                <label>SEO Meta Description <span class="text-light-gray" id="metaDescCount"></span></label>
                <textarea class="textarea-70h" id="metaDescInput" name="meta_description" maxlength="320" placeholder="{{ $post->excerpt }}">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>

            <div class="csp5-056">
                <div class="label-11">Google Search Preview</div>
                <div class="google-title" id="seoPreviewTitle"></div>
                <div class="forest-fs13">kegalle.com › blog › {{ $post->slug }}</div>
                <div class="google-desc" id="seoPreviewDesc"></div>
            </div>
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
<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    var bodyField = document.getElementById('editPostBody');
    var editQuill = new Quill('#editPostEditor', { theme: 'snow', placeholder: 'Write the article body here…' });
    editQuill.root.innerHTML = bodyField.value;
    bodyField.closest('form').addEventListener('submit', function () {
        bodyField.value = editQuill.root.innerHTML;
    });

    var fallbackTitle = {{ Js::from($post->title) }};
    var fallbackDesc = {{ Js::from($post->excerpt ?? '') }};
    var titleInput = document.getElementById('metaTitleInput');
    var descInput = document.getElementById('metaDescInput');
    var titleCount = document.getElementById('metaTitleCount');
    var descCount = document.getElementById('metaDescCount');
    var previewTitle = document.getElementById('seoPreviewTitle');
    var previewDesc = document.getElementById('seoPreviewDesc');

    function refresh() {
        var t = titleInput.value || fallbackTitle;
        var d = descInput.value || fallbackDesc;
        titleCount.textContent = titleInput.value.length + ' / 60 recommended';
        descCount.textContent = descInput.value.length + ' / 160 recommended';
        previewTitle.textContent = t;
        previewDesc.textContent = d;
    }

    titleInput.addEventListener('input', refresh);
    descInput.addEventListener('input', refresh);
    refresh();
})();
</script>
@endpush
@endsection
