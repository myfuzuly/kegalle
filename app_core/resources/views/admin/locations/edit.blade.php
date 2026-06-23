@extends('layouts.admin')

@section('title','Edit Location')
@section('page','Locations')
@section('heading','Edit Location')
@section('subheading','Update location hierarchy, type, slug URL and visibility')

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/locations">Back</a>
@endsection

@section('content')
<section class="sa-card">
    <form class="ka-premium-form" method="post" action="/admin/locations/{{ $location->id }}">
        @csrf
        @method('PUT')

        <div class="ka-form-grid">
            <div class="ka-field">
                <label>Parent Location</label>
                <select name="parent_id">
                    <option value="">No Parent</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $location->parent_id) == $parent->id)>
                            {{ $parent->name }} ({{ ucfirst($parent->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ka-field">
                <label>Name</label>
                <input name="name" value="{{ old('name', $location->name) }}" required>
            </div>

            <div class="ka-field">
                <label>Slug URL</label>
                <input name="slug" value="{{ old('slug', $location->slug) }}" required>
            </div>

            <div class="ka-field">
                <label>Type</label>
                <select name="type">
                    <option value="province" @selected(old('type', $location->type) === 'province')>Province</option>
                    <option value="district" @selected(old('type', $location->type) === 'district')>District</option>
                    <option value="city" @selected(old('type', $location->type) === 'city')>City</option>
                    <option value="town" @selected(old('type', $location->type) === 'town')>Town</option>
                </select>
            </div>

            <div class="ka-field">
                <label>Sort Order</label>
                <input name="sort_order" type="number" value="{{ old('sort_order', $location->sort_order ?? 0) }}">
            </div>

            <label class="ka-check">
                <input type="checkbox" name="is_active" value="1" @checked($location->is_active)>
                Active
            </label>
        </div>

        <div class="ka-form-actions">
            <button class="ka-btn ka-btn-primary">Update Location</button>
            <a class="ka-btn ka-btn-light" href="/admin/locations">Cancel</a>
        </div>
    </form>
</section>
@endsection
