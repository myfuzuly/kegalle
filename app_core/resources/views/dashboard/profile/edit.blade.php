@extends('layouts.dashboard')
@section('banner_sub', 'Manage your personal information and account security.')

@section('title','Profile Settings')
@section('eyebrow','Account')
@section('heading','Profile Settings')
@section('subheading','Update your personal details and password.')

@section('content')

@if(session('success'))
<div class="prf-alert prf-alert-success">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif
@if(session('warning'))
<div class="prf-alert prf-alert-warning">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  {{ session('warning') }}
</div>
@endif
@if($errors->any())
<div class="prf-alert prf-alert-error">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  {{ $errors->first() }}
</div>
@endif

@if(empty(auth()->user()->phone) || empty(auth()->user()->location_id))
<div class="prf-alert prf-alert-warning flex-g10-mb18">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span>Please add your <strong>phone number</strong> and <strong>location</strong> to access your dashboard and post ads.</span>
</div>
@endif

<div class="prf-grid">
  {{-- Personal Info --}}
  <div class="prf-card">
    <div class="prf-card-head">
      <div class="prf-card-head-icon bg-green50">👤</div>
      <h2>Personal Information</h2>
    </div>
    <div class="prf-card-body">
      <div class="prf-avatar-wrap" id="avatarWrap" style="cursor:pointer;display:inline-block;position:relative;margin-bottom:16px">
        @if(auth()->user()->avatar)
          <img id="avatarPreview" src="{{ auth()->user()->avatar }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:block;border:3px solid #e5e7eb">
        @else
          <div id="avatarInitial" class="prf-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
          <img id="avatarPreview" src="" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:none;border:3px solid #e5e7eb">
        @endif
        <span style="position:absolute;bottom:0;right:0;background:#16a34a;color:#fff;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:13px">✎</span>
      </div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:16px">Click photo to change · JPG/PNG/WEBP · max 2 MB</div>
      <form method="POST" action="/dashboard/profile" class="d-block" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp" style="display:none">
        <div class="prf-field">
          <label for="profile-name" class="prf-label">Full Name</label>
          <input id="profile-name" name="name" value="{{ old('name', $user->name) }}" required class="prf-input" placeholder="Your full name">
        </div>
        <div class="prf-field">
          <label for="profile-email" class="prf-label">Email Address</label>
          <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="prf-input" placeholder="your@email.com">
        </div>
        <div class="prf-field">
          <label for="profile-phone" class="prf-label">Phone Number</label>
          <input id="profile-phone" name="phone" value="{{ old('phone', $user->phone) }}" class="prf-input" placeholder="07X XXX XXXX" required>
        </div>
        <div class="prf-field">
          <label for="profile-location" class="prf-label">Location</label>
          <select id="profile-location" name="location_id" class="prf-select" required>
            <option value="">Select your location</option>
            @foreach($locations as $location)
              <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) == $location->id)>{{ $location->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="prf-submit">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Changes
        </button>
      </form>
    </div>
  </div>

  {{-- Change Password --}}
  <div class="prf-card">
    <div class="prf-card-head">
      <div class="prf-card-head-icon bg-rose50">🔐</div>
      <h2>Change Password</h2>
    </div>
    <div class="prf-card-body">
      <div class="notice-amber">
        <strong>Security tip:</strong> Use at least 8 characters with a mix of letters and numbers.
      </div>
      <form method="POST" action="/dashboard/profile/password" class="d-block">
        @csrf
        @method('PUT')
        <div class="prf-field">
          <label for="profile-new-password" class="prf-label">
            New Password
            <span class="prf-label-hint">(min 8 characters)</span>
          </label>
          <input id="profile-new-password" type="password" name="password" required minlength="8" autocomplete="new-password" class="prf-input" placeholder="Enter new password">
        </div>
        <div class="prf-field">
          <label for="profile-confirm-password" class="prf-label">Confirm New Password</label>
          <input id="profile-confirm-password" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="prf-input" placeholder="Repeat new password">
        </div>
        <button type="submit" class="prf-submit">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Update Password
        </button>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){
    var wrap    = document.getElementById('avatarWrap');
    var input   = document.getElementById('avatarInput');
    var preview = document.getElementById('avatarPreview');
    var initial = document.getElementById('avatarInitial');
    if(wrap && input){
        wrap.addEventListener('click', function(){ input.click(); });
        input.addEventListener('change', function(){
            var f = this.files[0];
            if(!f) return;
            preview.src = URL.createObjectURL(f);
            preview.style.display = 'block';
            if(initial) initial.style.display = 'none';
        });
    }
});
</script>
@endpush
