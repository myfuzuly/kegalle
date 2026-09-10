@extends('layouts.store-dashboard')

@section('title', 'Products — ' . ($store->name ?? 'Store'))
@section('eyebrow', 'Products')
@section('heading', 'Store Products')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}/products/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Add Product
</a>
@endsection

@section('content')

<div class="dbi-card">
    <div class="dbi-card-head">
        <h3>All Products <span class="text-13-light">({{ $products->total() }})</span></h3>
        <a class="pill-link" href="/dashboard/stores/{{ $store->id }}">← Overview</a>
    </div>

    @if($products->isNotEmpty())
    <div class="overflow-x-auto">
        <table class="dpt-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Added</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                @php
                    $pillCls = match($product->status ?? 'pending') {
                        'approved','active' => 'dbi-pill-green',
                        'rejected' => 'dbi-pill-red',
                        'sold' => 'dbi-pill-gray',
                        default => 'dbi-pill-amber',
                    };
                @endphp
                <tr>
                    <td class="mw-300">
                        <span class="dpt-title">{{ $product->title }}</span>
                        @if($product->category)
                        <span class="dpt-meta">{{ $product->category->name ?? '' }}</span>
                        @endif
                    </td>
                    <td class="tnum-fw6">
                        {{ $product->price ? 'Rs. '.number_format($product->price) : '—' }}
                    </td>
                    <td><span class="dbi-pill {{ $pillCls }}">{{ ucfirst($product->status ?? 'pending') }}</span></td>
                    <td class="tnum-slate">{{ number_format($product->views ?? 0) }}</td>
                    <td class="caption-nowrap">{{ $product->created_at?->format('M d, Y') ?? '—' }}</td>
                    <td class="nowrap">
                        <div class="flex-g6-ac">
                            <a href="/dashboard/stores/{{ $store->id }}/products/{{ $product->id }}/edit" class="dpt-btn dpt-btn-edit">Edit</a>
                            @if(in_array($product->status ?? '', ['approved','active']))
                            <a href="/listings/{{ $product->slug }}" target="_blank" class="dpt-btn dpt-btn-view">View</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="dpt-pagination">
        <span class="dpt-pagination-info">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</span>
        <div class="dpt-pagination-links">
            @if($products->onFirstPage())
                <span class="disabled">‹</span>
            @else
                <a href="{{ $products->previousPageUrl() }}">‹</a>
            @endif
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}">›</a>
            @else
                <span class="disabled">›</span>
            @endif
        </div>
    </div>
    @endif

    @else
    <div class="dbi-empty">
        <div class="emoji-40">📦</div>
        <strong>No products yet</strong>
        <p>Add your first product so buyers can discover your store.</p>
        <a href="/dashboard/stores/{{ $store->id }}/products/create" class="dbi-empty-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add First Product
        </a>
    </div>
    @endif
</div>

@endsection
