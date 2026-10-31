{{-- Phone-only sticky WhatsApp + Call bar on the ad page; replaces the bottom app bar there.
     Reveal JS: kRevealWaScb / kRevealCallScb in frontend/listings/show. CSS: kegalle-components.css (.k-contact-bar) --}}
@props(['wa', 'msg', 'tel', 'fmt'])
<div class="k-contact-bar" role="region" aria-label="Contact the seller">
    @if($wa)
    <button type="button" class="k-btn k-btn-whatsapp k-btn-center k-scb-wa" data-wa="{{ $wa }}" data-msg="{{ $msg }}">WhatsApp</button>
    @endif
    @if($tel)
    <button type="button" class="k-btn k-btn-call k-btn-center k-scb-call" data-tel="{{ $tel }}" data-fmt="{{ $fmt }}">📞 Call</button>
    @endif
</div>
