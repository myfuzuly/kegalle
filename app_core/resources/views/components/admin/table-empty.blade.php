{{-- Empty-state row for admin tables. Styles: .td-empty in kegalle-admin-ds.css --}}
@props(['colspan', 'message', 'hint' => null])
<tr><td colspan="{{ $colspan }}" class="td-empty">
    <strong class="td-empty-title">{{ $message }}</strong>
    @if($hint)<span class="td-empty-hint">{{ $hint }}</span>@endif
    {{ $slot }}
</td></tr>
