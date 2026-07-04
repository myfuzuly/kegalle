@extends('layouts.admin')
@section('title','Categories')
@section('page','Categories')
@section('heading','Category Management')
@section('subheading','Manage categories and sub-categories with slug URLs')
@section('content')
<section class="sa-card">
<div class="sa-card-head"><h2>Add Category / Sub Category</h2><span>Full CRUD</span></div>
<form class="ka-premium-form" method="post" action="/admin/categories" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Category Image (optional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        <small>JPG/PNG/WEBP, max 2MB. Falls back to the icon below if left empty.</small>
    </div>
    <div class="ka-field">
        <label>Parent Category</label>
        <select name="parent_id">
            <option value="">Main Category</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="ka-field">
        <label>Category Name</label>
        <input name="name" placeholder="e.g. Electronics" required>
    </div>
    <div class="ka-field">
        <label>Slug URL</label>
        <input name="slug" placeholder="auto-generated if empty">
    </div>
    <div class="ka-field">
        <label>Icon (emoji)</label>
        <input name="icon" placeholder="🛒">
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input name="sort_order" type="number" value="0">
    </div>
    <label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Add Category</button>
</div>
</form>
</section>

<section class="sa-card">
<div class="sa-card-head"><h2>Categories from Database</h2><span>{{ $categories->total() }} categories</span></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-categories"><thead><tr><th>Image</th><th>Name</th><th>Parent</th><th>Slug</th><th>Listings</th><th>Status</th><th>Sort</th><th>Action</th></tr></thead><tbody>
@forelse($categories as $category)
<tr>
    <td>
        @if($category->image)
            <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover">
        @else
            <span style="width:40px;height:40px;border-radius:8px;background:var(--ka-bg,#f5f7fb);display:flex;align-items:center;justify-content:center;font-size:18px">{{ $category->icon ?? '▦' }}</span>
        @endif
    </td>
    <td><b>{{ $category->name }}</b><small>#{{ $category->id }}</small></td>
    <td>{{ $category->parent->name ?? 'Main' }}</td>
    <td><small>{{ $category->slug }}</small></td>
    <td>{{ $category->listings_count }}</td>
    <td><span class="sa-status {{ $category->is_active ? 'active' : 'suspended' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td>{{ $category->sort_order ?? 0 }}</td>
    <td class="sa-actions-inline">
        <a href="/admin/categories/{{ $category->id }}/edit" title="Edit">Edit</a>
        <form method="post" action="/admin/categories/{{ $category->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form>
        <form method="post" action="/admin/categories/{{ $category->id }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="8">No categories found.</td></tr>
@endforelse
</tbody></table></div>{{ $categories->links() }}</section>
@endsection
