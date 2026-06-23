@extends('layouts.dashboard')

@section('title','Profile Settings')
@section('eyebrow','Account')
@section('heading','Profile Settings')
@section('subheading','Update your personal details and password.')

@section('content')
<div class="kd-grid-2">
    <section class="kd-card">
        <div class="kd-card-head"><h2>Personal Information</h2></div>
        <form method="POST" action="/dashboard/profile" style="display:grid;gap:14px">
            @csrf
            @method('PUT')
            <div>
                <label for="profile-name" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Full Name</label>
                <input id="profile-name" name="name" value="{{ old('name', $user->name) }}" required style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <div>
                <label for="profile-email" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Email Address</label>
                <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <div>
                <label for="profile-phone" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Phone Number</label>
                <input id="profile-phone" name="phone" value="{{ old('phone', $user->phone) }}" style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <div>
                <label for="profile-location" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Location</label>
                <select id="profile-location" name="location_id" style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
                    <option value="">Select location</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="kd-btn kd-btn-primary" type="submit" style="justify-self:start">Save Changes</button>
        </form>
    </section>

    <section class="kd-card">
        <div class="kd-card-head"><h2>Change Password</h2></div>
        <form method="POST" action="/dashboard/profile/password" style="display:grid;gap:14px">
            @csrf
            <div>
                <label for="profile-current-password" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Current Password</label>
                <input id="profile-current-password" type="password" name="current_password" required style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <div>
                <label for="profile-new-password" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">New Password</label>
                <input id="profile-new-password" type="password" name="password" required style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <div>
                <label for="profile-confirm-password" style="display:block;font-weight:700;margin-bottom:6px;font-size:13px">Confirm New Password</label>
                <input id="profile-confirm-password" type="password" name="password_confirmation" required style="width:100%;height:46px;border:1px solid var(--kd-line);border-radius:12px;padding:0 14px;font-family:inherit">
            </div>
            <button class="kd-btn kd-btn-primary" type="submit" style="justify-self:start">Update Password</button>
        </form>
    </section>
</div>
@endsection
