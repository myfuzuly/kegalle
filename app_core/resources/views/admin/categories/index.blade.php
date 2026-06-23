@extends('layouts.admin')
@section('title','Categories')
@section('page','Categories')
@section('heading','Category Management')
@section('subheading','Manage categories and sub-categories with slug URLs')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Add Category / Sub Category</h2><span>Full CRUD</span></div>
<form class="sa-form ka-category-form" method="post" action="/admin/categories">@csrf
<select name="parent_id"><option value="">Main Category</option>@foreach($parents as $parent)<option value="{{ $parent->id }}">{{ $parent->name }}</option>@endforeach</select>
<input name="name" placeholder="Category name" required><input name="slug" placeholder="Slug URL optional"><input name="icon" placeholder="Icon"><input name="sort_order" type="number" placeholder="Sort" value="0"><label class="ka-check"><input type="checkbox" name="is_active" value="1" checked> Active</label><button>Add Category</button></form></section>
<section class="sa-card"><div class="sa-card-head"><h2>Categories from Database</h2><span>{{ $categories->total() }} categories</span></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Category</th><th>Parent</th><th>Slug</th><th>Listings</th><th>Status</th><th>Sort</th><th>Actions</th></tr></thead><tbody>
@forelse($categories as $category)<tr><td><b>{{ $category->icon ?? '▦' }} {{ $category->name }}</b><small>#{{ $category->id }}</small></td><td>{{ $category->parent->name ?? 'Main' }}</td><td>{{ $category->slug }}</td><td>{{ $category->listings_count }}</td><td><span class="sa-status {{ $category->is_active ? 'active' : 'suspended' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td><td>{{ $category->sort_order ?? 0 }}</td><td class="sa-actions-inline"><a href="/admin/categories/{{ $category->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/categories/{{ $category->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form><form method="post" action="/admin/categories/{{ $category->id }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form></td></tr>@empty<tr><td colspan="7">No categories found.</td></tr>@endforelse
</tbody></table></div>{{ $categories->links() }}</section>
@endsection
