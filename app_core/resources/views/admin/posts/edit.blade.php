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

        <div style="border-top:1px solid #E5E8EF;margin-top:18px;padding-top:16px">
            <div style="font-weight:800;font-size:14px;margin-bottom:4px;color:#0D1B2A">🔍 SEO Settings</div>
            <p style="font-size:12px;color:#667085;margin-bottom:14px">Falls back to the article title and excerpt if left blank.</p>

            <div class="ka-field" style="margin-bottom:10px">
                <label>SEO Meta Title <span id="metaTitleCount" style="font-weight:400;color:#667085"></span></label>
                <input id="metaTitleInput" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="180" placeholder="{{ $post->title }}">
            </div>
            <div class="ka-field" style="margin-bottom:14px">
                <label>SEO Meta Description <span id="metaDescCount" style="font-weight:400;color:#667085"></span></label>
                <textarea id="metaDescInput" name="meta_description" maxlength="320" style="width:100%;min-height:70px;border:1px solid #E5E8EF;border-radius:8px;padding:10px;font-family:inherit" placeholder="{{ $post->excerpt }}">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>

            <div style="border:1px solid #E5E8EF;border-radius:10px;padding:14px;background:#F8FAFC">
                <div style="font-size:11px;color:#667085;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">Google Search Preview</div>
                <div style="color:#1a0dab;font-size:18px;line-height:1.3;font-family:Arial,sans-serif" id="seoPreviewTitle"></div>
                <div style="color:#006621;font-size:13px;margin:2px 0">kurulla.com › blog › {{ $post->slug }}</div>
                <div style="color:#545454;font-size:13px;line-height:1.4;font-family:Arial,sans-serif" id="seoPreviewDesc"></div>
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
<script>
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
