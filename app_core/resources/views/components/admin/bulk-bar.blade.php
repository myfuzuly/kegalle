{{-- Sticky bar for acting on selected table rows. Rows join it with <x-admin.row-check form="{{ id }}">.
     Behaviour: kegalle-admin-ui.js "Bulk selection"; styles: .kx-bulkbar in kegalle-admin-ds.css --}}
@props(['id', 'action', 'confirm' => null])
<form id="{{ $id }}" method="post" action="{{ $action }}" class="kx-bulkbar" data-bulk-bar
      @if($confirm) data-confirm="{{ $confirm }}" @endif hidden>
    @csrf
    <span class="kx-bulkbar-count" data-bulk-count aria-live="polite">0 selected</span>
    {{ $slot }}
    <button type="button" class="ka-btn" data-bulk-clear>Clear</button>
</form>
