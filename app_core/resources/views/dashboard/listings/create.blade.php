@extends('layouts.app')
@section('title','Post Listing - Kegalle')
@section('content')
<section class="kd-wrap">
    <div class="kd-container kd-form-container">
        <div class="kd-head">
            <div>
                <span>Seller Dashboard</span>
                <h1>Post New Listing</h1>
                <p>Add a store product or individual classified ad.</p>
            </div>
        </div>

        <form class="kd-form" method="post" action="/dashboard/listings" enctype="multipart/form-data">
            @csrf
            <div class="kd-form-grid">
                <label>Listing Type
                    <select name="type" required>
                        <option value="product">Store Product</option>
                        <option value="classified">Classified Ad</option>
                    </select>
                </label>

                <label>Store
                    <select name="store_id">
                        <option value="">No store / Individual classified</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Title
                    <input name="title" required placeholder="Example: iPhone 15 Pro Max">
                </label>

                <label>Category
                    <select name="category_id">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label>Price
                    <input name="price" type="number" min="0" step="0.01" placeholder="Price in LKR">
                </label>

                <label>Location
                    <input name="location" placeholder="Kegalle">
                </label>
            </div>

            <label>Description
                <textarea name="description" required placeholder="Describe condition, features, warranty, delivery, etc."></textarea>
            </label>

            <label>Images
                <input type="file" name="images[]" multiple accept="image/*">
                <small>You can upload multiple JPG, PNG or WEBP images.</small>
            </label>

            <button class="kd-primary" type="submit">Submit for Approval</button>
        </form>
    </div>
</section>
@endsection
