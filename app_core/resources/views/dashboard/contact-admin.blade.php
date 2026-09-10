@extends('layouts.dashboard')
@section('banner_sub', 'Need help? Send a message directly to the kegalle admin team.')
@section('title','Contact Administrator')
@section('heading','Contact Admin')
@section('subheading','Send a message directly to the kegalle admin. We\'ll reply to your registered email.')

@section('content')

@php
  $waUser  = auth()->user();
  $waStore = \App\Models\Store::where('user_id', $waUser->id)->orderByDesc('created_at')->first();
  $waText  = 'Hi, I am ' . $waUser->name . ($waStore ? ' from ' . $waStore->name : '') . ' on kegalle. I need help with: ';
  $waLink  = 'https://wa.me/94712930930?text=' . rawurlencode($waText);
@endphp

@if(session('success'))
<div class="ca-success">
  <span class="fs28-ns">✅</span>
  <div>
    <div class="fw8-fs15-green">Message Sent!</div>
    <div class="fs135-green700">{{ session('success') }}</div>
  </div>
</div>
@endif

<a href="{{ $waLink }}" target="_blank" class="ca-wa-banner">
  <svg width="42" height="42" viewBox="0 0 24 24" fill="#fff" class="flex-shrink-0">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.558 4.117 1.533 5.845L0 24l6.335-1.52A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.032-1.384l-.361-.214-3.761.902.938-3.668-.235-.376A9.808 9.808 0 012.182 12C2.182 6.58 6.58 2.182 12 2.182S21.818 6.58 21.818 12 17.42 21.818 12 21.818z"/>
  </svg>
  <div class="ca-wa-banner-text">
    <div class="ca-wa-banner-title">WhatsApp Admin — Fastest Response</div>
    <div class="ca-wa-banner-sub">Tap to open WhatsApp and chat with the admin instantly · +94 712 930 930</div>
  </div>
  <div class="ca-wa-banner-btn">Open WhatsApp →</div>
  <div class="ca-wa-bubble"></div>
</a>

<div class="ca-layout">

  <div class="ca-form-card">
    <div class="ca-card-head">
      <h2>Send a Message</h2>
      <span>Replies sent to your email</span>
    </div>
    <div class="ca-card-body">
      <form method="POST" action="/dashboard/contact-admin" id="contactAdminForm">
        @csrf

        <div class="ca-sender-grid">
          <div>
            <label class="ca-field-label">Your Name</label>
            <div class="ca-field-static">{{ auth()->user()->name }}</div>
          </div>
          <div>
            <label class="ca-field-label">Reply-to Email</label>
            <div class="ca-field-static">{{ auth()->user()->email }}</div>
          </div>
        </div>

        <div class="mb-16">
          <label class="ca-field-label title-14">Subject *</label>
          <select name="subject" required class="ca-field-select">
            <option value="">— Select a topic —</option>
            <option value="Store Limit Increase Request"    {{ old('subject')==='Store Limit Increase Request'    ? 'selected':'' }}>🏪 Store Limit Increase Request</option>
            <option value="Store Approval Issue"            {{ old('subject')==='Store Approval Issue'            ? 'selected':'' }}>⏳ Store Approval Issue</option>
            <option value="Listing Rejected — Need Help"   {{ old('subject')==='Listing Rejected — Need Help'   ? 'selected':'' }}>📋 Listing Rejected — Need Help</option>
            <option value="Deal Approval Issue"             {{ old('subject')==='Deal Approval Issue'             ? 'selected':'' }}>🔥 Deal Approval Issue</option>
            <option value="Account Issue"                   {{ old('subject')==='Account Issue'                   ? 'selected':'' }}>👤 Account Issue</option>
            <option value="Payment / Membership"            {{ old('subject')==='Payment / Membership'            ? 'selected':'' }}>💳 Payment / Membership</option>
            <option value="Report a Bug"                    {{ old('subject')==='Report a Bug'                    ? 'selected':'' }}>🐛 Report a Bug</option>
            <option value="Other"                           {{ old('subject')==='Other'                           ? 'selected':'' }}>💬 Other</option>
          </select>
          @error('subject')<div class="ca-field-error">{{ $message }}</div>@enderror
        </div>

        <div class="mb-22">
          <label class="ca-field-label title-14">Message *</label>
          <textarea name="message" required rows="7" id="contactMsg" maxlength="3000" class="ca-field-textarea" placeholder="Describe your issue or request in detail. The more specific you are, the faster we can help.">{{ old('message') }}</textarea>
          <div class="ca-char-row">
            @error('message')<div class="ca-field-error">{{ $message }}</div>@else<div></div>@enderror
            <div id="msgCount" class="ca-char-count">0 / 3000</div>
          </div>
        </div>

        <button type="submit" id="contactSubmitBtn" class="ca-submit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Message to Admin
        </button>
      </form>
    </div>
  </div>

  <div>
    <div class="ca-side-card border-green100">
      <div class="ca-side-title">📬 What happens next?</div>
      <ol>
        <li>Your message goes directly to the super admin.</li>
        <li>Admin reviews your request carefully.</li>
        <li>A reply is sent to <strong>{{ auth()->user()->email }}</strong>.</li>
        <li>Most requests handled within 24 hours.</li>
      </ol>
    </div>

    <div class="ca-side-card border-wa-bg">
      <div class="ca-side-title text-whatsapp">💬 WhatsApp (Fastest)</div>
      <p class="fs13-dark-mb12">Get a faster response — message us directly on WhatsApp.</p>
      <a href="{{ $waLink }}" target="_blank" class="ca-wa-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.558 4.117 1.533 5.845L0 24l6.335-1.52A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 01-5.032-1.384l-.361-.214-3.761.902.938-3.668-.235-.376A9.808 9.808 0 012.182 12C2.182 6.58 6.58 2.182 12 2.182S21.818 6.58 21.818 12 17.42 21.818 12 21.818z"/></svg>
        Chat on WhatsApp
      </a>
      <div class="center-fs12-mt8">+94 70 693 0930</div>
    </div>

    <div class="ca-side-card border-blue50">
      <div class="ca-side-title text-blue900">📧 Email</div>
      <p class="fs13-gray-mb12">Alternatively, email us and we'll reply within 24 hours.</p>
      <a href="mailto:support@kegalle.com" class="ca-email-btn">📧 support@kegalle.com</a>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){
  var msg   = document.getElementById('contactMsg');
  var count = document.getElementById('msgCount');
  if(msg){
    msg.addEventListener('input', function(){ count.textContent = this.value.length + ' / 3000'; });
    count.textContent = msg.value.length + ' / 3000';
  }
  var btn = document.getElementById('contactSubmitBtn');
  document.getElementById('contactAdminForm').addEventListener('submit', function(){
    if(btn){ btn.disabled=true; btn.textContent='Sending…'; btn.style.opacity='.7'; }
  });
});
</script>
@endpush
