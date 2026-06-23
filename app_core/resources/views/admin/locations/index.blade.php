@extends('layouts.admin')

@section('title','Locations')
@section('page','Locations')
@section('heading','Location Management')
@section('subheading','Manage province, district, city and town hierarchy with editable slug URLs')

@section('content')

<section class="sa-card">
    <div class="sa-card-head">
        <h2>Add New Location</h2>
        <span>Province / District / City / Town</span>
    </div>

    <form method="POST" action="{{ route('admin.locations.store') }}" class="ka-premium-form">
        @csrf

        <div class="ka-form-grid">
            <div class="ka-field">
                <label>Parent Location</label>
                <select name="parent_id">
                    <option value="">No Parent</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">
                            {{ $parent->parent ? $parent->parent->name . ' / ' : '' }}{{ $parent->name }} ({{ ucfirst($parent->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="ka-field">
                <label>Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Location name"
                    required>
            </div>

            <div class="ka-field">
                <label>Slug URL</label>
                <input
                    type="text"
                    name="slug"
                    value="{{ old('slug') }}"
                    placeholder="auto-generate if empty">
            </div>

            <div class="ka-field">
                <label>Type</label>
                <select name="type" required>
                    <option value="province" @selected(old('type') === 'province')>Province</option>
                    <option value="district" @selected(old('type') === 'district')>District</option>
                    <option value="city" @selected(old('type', 'city') === 'city')>City</option>
                    <option value="town" @selected(old('type') === 'town')>Town</option>
                </select>
            </div>

            <div class="ka-field">
                <label>Sort Order</label>
                <input
                    name="sort_order"
                    type="number"
                    value="{{ old('sort_order', 0) }}">
            </div>

            <label class="ka-check">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    checked>
                Active
            </label>
        </div>

        <div class="ka-form-actions">
            <button type="submit" class="ka-btn ka-btn-primary">+ Add Location</button>
        </div>
    </form>
</section>

<section class="sa-card">
    <div class="sa-head">
        <div>
            <p>Super Admin</p>
            <h1>Locations from Database</h1>
        </div>

        <form class="sa-search" method="get">
            <input name="q" value="{{ request('q') }}" placeholder="Search location or slug">

            <select name="type">
                <option value="">All Types</option>
                <option value="province" @selected(request('type') === 'province')>Province</option>
                <option value="district" @selected(request('type') === 'district')>District</option>
                <option value="city" @selected(request('type') === 'city')>City</option>
                <option value="town" @selected(request('type') === 'town')>Town</option>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <div class="sa-card-head">
        <h2>Locations from Database</h2>
        <span>{{ $locations->total() }} locations</span>
    </div>

    <div class="sa-table-wrap">
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Location</th>
                    <th>Parent</th>
                    <th>Type</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Sort</th>
                    <th class="ka-action-th">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td>
                            <b>{{ $location->name }}</b>
                            <small>#{{ $location->id }}</small>
                        </td>

                        <td>{{ $location->parent->name ?? '-' }}</td>

                        <td>{{ ucfirst($location->type) }}</td>

                        <td>{{ $location->slug }}</td>

                        <td>
                            <span class="sa-status {{ $location->is_active ? 'active' : 'suspended' }}">
                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>{{ $location->sort_order }}</td>

                        <td class="sa-actions-inline">
                            <a href="/admin/locations/{{ $location->id }}/edit" title="Edit">Edit</a>

                            <form method="post" action="/admin/locations/{{ $location->id }}/toggle">
                                @csrf
                                <button title="Toggle">Toggle</button>
                            </form>

                            <form
                                method="post"
                                action="/admin/locations/{{ $location->id }}"
                                onsubmit="return confirm('Delete this location?')">
                                @csrf
                                @method('DELETE')
                                <button class="danger" title="Delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No locations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $locations->links() }}
</section>

@endsection