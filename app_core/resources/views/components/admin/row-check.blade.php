{{-- Row checkbox for a bulk bar. Pass :all="true" for the header "select all" box. --}}
@props(['form', 'value' => null, 'label', 'all' => false])
@if($all)
<input type="checkbox" class="kx-row-check" data-bulk-all aria-label="{{ $label }}">
@else
<input type="checkbox" name="ids[]" value="{{ $value }}" form="{{ $form }}" class="kx-row-check" aria-label="{{ $label }}">
@endif
