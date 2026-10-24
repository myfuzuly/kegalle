{{-- Export link that keeps the current filters (query string). --}}
@props(['href', 'label' => 'Export CSV'])
@php $qs = request()->getQueryString(); @endphp
<a href="{{ $href }}{{ $qs ? '?'.$qs : '' }}" class="ka-btn" download>{{ $label }}</a>
