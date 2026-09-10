@extends('layouts.app')
@section('title','Contact Us · Kegalle Marketplace')
@section('meta_description','Get in touch with the Kegalle Marketplace team — send us a message, call, or chat with us on WhatsApp.')
@section('content')

{{-- Hero --}}
<div class="cu-hero">
    <div class="cu-hero-orb cu-hero-orb1"></div>
    <div class="cu-hero-orb cu-hero-orb2"></div>
    <div class="cu-hero-inner">
        <div class="cu-hero-badge">
            <span class="cu-hero-badge-dot"></span>
            We're Online
        </div>
        <h1 class="cu-hero-h1">We're Here <em>to Help</em></h1>
        <p class="cu-hero-p">Questions, feedback, or need help with a listing? Our local team in Kegalle responds within 24 hours.</p>
        <div class="cu-hero-stats">
            <div class="cu-hero-stat"><strong>&lt; 24h</strong><span>Response time</span></div>
            <div class="cu-hero-stat-div"></div>
            <div class="cu-hero-stat"><strong>+94 712 930 930</strong><span>Call us free</span></div>
            <div class="cu-hero-stat-div"></div>
            <div class="cu-hero-stat"><strong>WhatsApp</strong><span>Instant chat</span></div>
        </div>
    </div>
</div>

<div class="cu-wrap">

    {{-- Form --}}
    <div class="cu-card">
        <div class="cu-card-accent"></div>
        <div class="cu-card-body">
            <div class="cu-card-head">
                <div class="cu-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <div>
                    <div class="cu-card-title">Send Us a Message</div>
                    <div class="cu-card-sub">We'll get back to you as soon as possible.</div>
                </div>
            </div>

            @if(session('success'))
            <div class="cu-alert-ok">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="cu-alert-err">
                @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
            </div>
            @endif

            <form method="POST" action="/contact-us" id="cu-form">
                @csrf
                <div class="cu-row">
                    <div class="cu-field">
                        <label class="cu-label" for="cu-name">Full Name</label>
                        <input id="cu-name" class="cu-input" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" placeholder="Your name" required>
                    </div>
                    <div class="cu-field">
                        <label class="cu-label" for="cu-email">Email Address</label>
                        <input id="cu-email" class="cu-input" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="you@email.com" required>
                    </div>
                </div>
                <div class="cu-field">
                    <label class="cu-label" for="cu-subject">Subject</label>
                    <select id="cu-subject" class="cu-select" name="subject" required>
                        <option value="">Choose a subject…</option>
                        @php $subjects=['General Inquiry','Advertise with Us','Report a Problem','Account Help','Partnership','Other']; @endphp
                        @foreach($subjects as $s)
                            <option value="{{ $s }}" @selected(old('subject',request('subject'))===$s||(request('subject')==='advertise'&&$s==='Advertise with Us'))>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cu-field">
                    <label class="cu-label" for="cu-message">Your Message</label>
                    <textarea id="cu-message" class="cu-textarea" name="message" placeholder="Tell us how we can help you…" maxlength="2000" required>{{ old('message') }}</textarea>
                    <div class="cu-char"><span id="cu-count">{{ strlen(old('message','')) }}</span>/2000</div>
                </div>
                <button type="submit" id="cu-submit" class="cu-submit">
                    <span class="cu-submit-text">Send Message</span>
                    <span class="cu-submit-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </span>
                </button>
            </form>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="cu-sidebar">

        {{-- WhatsApp --}}
        <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I need help with Kegalle Marketplace.') }}" target="_blank" rel="noopener" class="cu-wa">
            <div class="cu-wa-bg"></div>
            <div class="cu-wa-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div class="cu-wa-content">
                <div class="cu-wa-title">Chat on WhatsApp</div>
                <div class="cu-wa-num">+94 712 930 930</div>
                <div class="cu-wa-tag">Typically replies instantly</div>
            </div>
            <div class="cu-wa-arrow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </a>

        {{-- Contact Details --}}
        <div class="cu-info-card">
            <div class="cu-info-hd">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                Contact Details
            </div>
            <div class="cu-info-rows">
                <a href="tel:+94712930930" class="cu-irow">
                    <div class="cu-irow-icon k-icon-green">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.014 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                    </div>
                    <div class="cu-irow-body">
                        <div class="cu-irow-lbl">Hotline</div>
                        <div class="cu-irow-val">+94 712 930 930</div>
                    </div>
                    <svg class="cu-irow-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="tel:+94352234433" class="cu-irow">
                    <div class="cu-irow-icon k-icon-orange">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    </div>
                    <div class="cu-irow-body">
                        <div class="cu-irow-lbl">Telephone</div>
                        <div class="cu-irow-val">+94 35 223 44 33</div>
                    </div>
                    <svg class="cu-irow-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <a href="mailto:support@kegalle.com" class="cu-irow">
                    <div class="cu-irow-icon k-icon-blue">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div class="cu-irow-body">
                        <div class="cu-irow-lbl">Email</div>
                        <div class="cu-irow-val">support@kegalle.com</div>
                    </div>
                    <svg class="cu-irow-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                <div class="cu-irow cu-irow-plain">
                    <div class="cu-irow-icon k-icon-purple">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="cu-irow-body">
                        <div class="cu-irow-lbl">Location</div>
                        <div class="cu-irow-val">Kegalle, Sri Lanka</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Office Hours --}}
        <div class="cu-info-card">
            <div class="cu-info-hd cu-info-hd-flex">
                <span class="cu-info-hd-flex-inner">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Office Hours
                </span>
                <span id="cu-status-badge"></span>
            </div>
            <div class="cu-hours-rows">
                <div class="cu-hrow">
                    <span class="cu-hday">Mon – Fri</span>
                    <span class="cu-htime">9:00 AM – 6:00 PM</span>
                </div>
                <div class="cu-hrow">
                    <span class="cu-hday">Saturday</span>
                    <span class="cu-htime">9:00 AM – 2:00 PM</span>
                </div>
                <div class="cu-hrow">
                    <span class="cu-hday">Sunday</span>
                    <span class="cu-htime cu-closed">Closed</span>
                </div>
            </div>
            <div class="cu-hours-note">All times are Sri Lanka Standard Time (GMT+5:30)</div>
        </div>

    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var ta=document.getElementById('cu-message'),cnt=document.getElementById('cu-count');
    if(ta&&cnt){ta.addEventListener('input',function(){cnt.textContent=ta.value.length;});cnt.textContent=ta.value.length;}
    var form=document.getElementById('cu-form'),btn=document.getElementById('cu-submit');
    if(form){form.addEventListener('submit',function(){if(btn){btn.disabled=true;btn.querySelector('.cu-submit-text').textContent='Sending…';}});}
    var badge=document.getElementById('cu-status-badge');
    if(badge){
        var now=new Date(),day=now.getDay(),h=now.getHours()+now.getMinutes()/60;
        var open=(day>=1&&day<=5&&h>=9&&h<18)||(day===6&&h>=9&&h<14);
        badge.innerHTML='<span class="cu-pill '+(open?'cu-pill-open':'cu-pill-closed')+'"><i class="cu-pill-dot"></i>'+(open?'Open Now':'Closed')+'</span>';
    }
})();
</script>
@endsection
