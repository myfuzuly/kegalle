@extends('layouts.admin')

@section('title','Edit Category')
@section('page','Categories')
@section('heading','Edit Category')
@section('subheading','Update category name, icon, slug URL and visibility')

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/categories">Back</a>
@endsection

@section('content')
<section class="sa-card">
    <form class="ka-premium-form" method="post" action="/admin/categories/{{ $category->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="ka-form-grid">
            <div class="ka-field ka-span-2">
                <label>Category Image</label>
                @if($category->image)
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                        <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" style="width:90px;height:90px;border-radius:12px;object-fit:cover;border:1.5px solid var(--ka-border, #E5E8EF)">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                            <input type="checkbox" name="remove_image" value="1"> Remove current image
                        </label>
                    </div>
                @else
                    <p style="font-size:13px;color:#667085;margin-bottom:8px">No image uploaded. The emoji icon below is used as a fallback.</p>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <small>JPG/PNG/WEBP, max 2MB. Uploading a new image replaces the current one.</small>
            </div>
            <div class="ka-field">
                <label>Name</label>
                <input name="name" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="ka-field">
                <label>Slug URL</label>
                <input name="slug" value="{{ old('slug', $category->slug) }}" required>
            </div>

            <div class="ka-field">
                <label>Icon</label>
                <input name="icon" value="{{ old('icon', $category->icon) }}">
            </div>

            <div class="ka-field">
                <label>Sort Order</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            </div>

            <label class="ka-check">
                <input type="checkbox" name="is_active" value="1" @checked($category->is_active)>
                Active
            </label>
        </div>

        <div class="ka-form-actions">
            <button class="ka-btn ka-btn-primary">Update Category</button>
            <a class="ka-btn ka-btn-light" href="/admin/categories">Cancel</a>
        </div>
    </form>
</section>
@endsection
