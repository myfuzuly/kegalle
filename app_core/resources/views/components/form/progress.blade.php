{{-- Sticky "n of m steps done" bar. Counts .alc-chk[data-done] items inside the element given by `checklist`.
     Behaviour: js/kegalle-ad-form.js; styles: .kaf-progress in kegalle-dashboard.css --}}
@props(['checklist'])
<div class="kaf-progress" role="status" data-form-progress="{{ $checklist }}">
    <div class="kaf-progress-track"><div class="kaf-progress-fill" data-progress-fill></div></div>
    <span class="kaf-progress-label" data-progress-label></span>
</div>
