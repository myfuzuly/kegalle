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
    <form class="ka-premium-form" method="post" action="/admin/categories/{{ $category->id }}">
        @csrf
        @method('PUT')

        <div class="ka-form-grid">
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
