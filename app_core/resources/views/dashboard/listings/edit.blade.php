@extends('layouts.app')
@section('title','Edit Listing - Kegalle')
@push('styles')
<link rel="stylesheet" href="/css/kegalle-dashboard-functional.css?v=2">
@endpush
@section('content')
<section class="kd-wrap">
    <div class="kd-container kd-form-container">
        <div class="kd-head">
            <div>
                <span>Seller Dashboard</span>
                <h1>Edit Listing</h1>
                <p>Update your listing details. Changes are reviewed before going live again.</p>
            </div>
        </div>

        <form class="kd-form" method="post" action="/dashboard/listings/{{ $listing->id }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($stores->count() > 1)
            <label style="margin-bottom:16px;display:block">Post to Store
                <select name="store_id">
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" @selected($listing->store_id == $s->id)>{{ $s->name }} · {{ $s->city ?? 'Kegalle' }}</option>
                    @endforeach
                </select>
            </label>
            @endif

            <div class="kd-form-grid">
                <label>Title
                    <input name="title" required value="{{ old('title', $listing->title) }}">
                </label>

                <label>Category
                    <select name="category_id" id="cf-category-select" required>
                        <option value="">Search or select category...</option>
                        @foreach($categories->whereNull('parent_id') as $parent)
                            <optgroup label="{{ $parent->icon }} {{ $parent->name }}">
                                @foreach($categories->where('parent_id', $parent->id) as $sub)
                                    <option value="{{ $sub->id }}" @selected($listing->category_id == $sub->id)>{{ $sub->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </label>

                <label>Price
                    <input name="price" type="number" min="0" step="0.01" inputmode="decimal" value="{{ old('price', $listing->price) }}" placeholder="Price in LKR">
                </label>
            </div>

            <div id="category-fields-container"></div>

            <label>Description
                <textarea name="description" required>{{ old('description', $listing->description) }}</textarea>
            </label>

            <label>Current Images</label>
            @if($listing->images->count())
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:14px">
                    @foreach($listing->images as $image)
                        <label style="position:relative;width:110px;cursor:pointer">
                            <img src="{{ asset('storage/'.$image->path) }}" alt="Listing image" style="width:110px;height:110px;object-fit:cover;border-radius:10px;border:1.5px solid #E5E8EF">
                            <span style="position:absolute;top:6px;right:6px;background:rgba(255,255,255,.92);border-radius:6px;padding:2px 6px;display:flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#D32F2F">
                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Delete
                            </span>
                        </label>
                    @endforeach
                </div>
            @else
                <p style="font-size:13px;color:#667085;margin-bottom:10px">No images uploaded yet.</p>
            @endif

            <label>Add More Images
                <input type="file" name="images[]" multiple accept="image/*">
                <small>JPG, PNG or WEBP. Check "Delete" on a photo above and save to remove it.</small>
            </label>

            <div style="background:#fff3e0;border-radius:10px;padding:12px 16px;margin:14px 0;font-size:13px;color:#e65100">
                Saving changes will send this listing back for admin approval before it appears publicly again.
            </div>

            <button class="kd-primary" type="submit">Save Changes</button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
window.listingVariants = {!! json_encode($listing->variants->mapWithKeys(fn ($v) => [$v->name => $v->price])->toArray()) !!};
window.cfExistingValues = {!! json_encode(
    array_merge(
        $listing->condition ? ['cf_condition' => $listing->condition] : [],
        $listing->values->filter(fn ($v) => $v->field !== null)->mapWithKeys(fn ($v) => ['cf_' . $v->field->name => $v->value])->toArray()
    )
) !!};
</script>
<script src="/js/category-fields.js?v=10"></script>
@endpush
