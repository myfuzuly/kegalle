@extends('layouts.admin')
@section('title','Edit Event')
@section('page','Events')
@section('heading','Edit Event #' . $event->id)
@section('subheading', \Illuminate\Support\Str::limit($event->title, 50))
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/events">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/events/{{ $event->id }}">
@csrf @method('PUT')

@if($errors->any())<div style="background:#FFEBEE;color:#C62828;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

<div class="ka-form-grid">
    <div class="ka-field ka-span-2"><label>Event Title *</label><input name="title" value="{{ old('title', $event->title) }}" required></div>
    <div class="ka-field ka-span-2"><label>Description</label><textarea name="description" rows="5">{{ old('description', $event->description) }}</textarea></div>

    <div class="ka-field"><label>Event Date *</label><input type="date" name="event_date" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required></div>
    <div class="ka-field"><label>Start Time</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"></div>
    <div class="ka-field"><label>End Time</label><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"></div>
    <div class="ka-field"><label>Event Type</label><select name="event_type"><option value="offline" @selected(old('event_type', $event->event_type)==='offline')>Offline (In-Person)</option><option value="online" @selected(old('event_type', $event->event_type)==='online')>Online</option><option value="hybrid" @selected(old('event_type', $event->event_type)==='hybrid')>Hybrid</option></select></div>

    <div class="ka-field"><label>Venue</label><input name="venue" value="{{ old('venue', $event->venue) }}"></div>
    <div class="ka-field"><label>Location *</label><select name="location" required><option value="">Select Location</option>@foreach($locations as $loc)<option value="{{ $loc->name }}" @selected(old('location', $event->location)===$loc->name)>{{ $loc->parent ? $loc->parent->name.' / ' : '' }}{{ $loc->name }} ({{ ucfirst($loc->type) }})</option>@endforeach</select></div>

    <div class="ka-field"><label>Price (LKR)</label><input type="number" name="price" step="0.01" value="{{ old('price', $event->price) }}"></div>
    <div class="ka-field"><label>Capacity</label><input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}"></div>

    <div class="ka-field"><label>Organizer Name</label><input name="organizer_name" value="{{ old('organizer_name', $event->organizer_name) }}"></div>
    <div class="ka-field"><label>Category</label><select name="category_id"><option value="">No Category</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(old('category_id', $event->category_id)==$cat->id)>{{ $cat->name }}</option>@endforeach</select></div>

    <div class="ka-field"><label>Post As User</label><select name="user_id"><option value="">Select User</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('user_id', $event->user_id)==$user->id)>{{ $user->name }} — {{ $user->email }}</option>@endforeach</select></div>
    <div class="ka-field"><label>Store</label><select name="store_id"><option value="">No Store</option>@foreach($stores as $store)<option value="{{ $store->id }}" @selected(old('store_id', $event->store_id)==$store->id)>{{ $store->name }}</option>@endforeach</select></div>

    <div class="ka-field"><label>Status</label><select name="status"><option value="published" @selected(old('status', $event->status)==='published')>Published</option><option value="pending" @selected(old('status', $event->status)==='pending')>Pending</option><option value="draft" @selected(old('status', $event->status)==='draft')>Draft</option><option value="rejected" @selected(old('status', $event->status)==='rejected')>Rejected</option><option value="cancelled" @selected(old('status', $event->status)==='cancelled')>Cancelled</option></select></div>
    <div class="ka-field"><label>Admin Note</label><input name="admin_note" value="{{ old('admin_note', $event->admin_note) }}"></div>

    <label class="ka-check"><input type="checkbox" name="is_free" value="1" @checked(old('is_free', $event->is_free))> Free Event</label>
    <label class="ka-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $event->is_featured))> Featured Event</label>
</div>

<div style="background:#F5F7FB;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;color:#666">
    <b>Stats:</b> {{ number_format($event->views ?? 0) }} views · Created {{ $event->created_at?->diffForHumans() }} · Slug: <code>{{ $event->slug }}</code>
</div>

<div class="ka-form-actions"><button class="ka-btn ka-btn-primary">Update Event</button><a href="/admin/events" class="ka-btn ka-btn-light">Cancel</a></div>
</form>
</section>
@endsection
