@extends('layouts.dashboard')
@section('title','Post a Service')
@section('eyebrow','Services')
@section('heading','Post a Service')

@section('content')
<div class="kd-form-card">
  <form method="POST" action="{{ route('dashboard.services.store') }}" enctype="multipart/form-data" class="kd-form">
    @csrf

    @if($errors->any())
    <div class="kd-alert kd-alert-error">
      <ul class="kd-alert-list">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="kd-form-section">
      <div class="kd-form-section-title">Service Details</div>

      <div class="kd-field">
        <label class="kd-label">Service Title <span class="kd-req">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}" class="kd-input @error('title') is-invalid @enderror"
               placeholder="e.g. Professional Real Estate Brokering" required maxlength="180">
        @error('title')<div class="kd-error">{{ $message }}</div>@enderror
      </div>

      <div class="kd-field-row">
        <div class="kd-field">
          <label class="kd-label">Category</label>
          <select name="category_id" class="kd-select">
            <option value="">-- Select Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="kd-field">
          <label class="kd-label">Service Type <span class="kd-req">*</span></label>
          <select name="service_type" class="kd-select @error('service_type') is-invalid @enderror" required>
            @foreach([
              'consulting'    => 'Consulting',
              'repair'        => 'Repair & Maintenance',
              'transport'     => 'Transport & Delivery',
              'brokering'     => 'Brokering / Agent',
              'construction'  => 'Construction & Renovation',
              'legal'         => 'Legal & Financial',
              'it'            => 'IT & Technology',
              'education'     => 'Education & Tutoring',
              'healthcare'    => 'Healthcare & Wellness',
              'beauty'        => 'Beauty & Personal Care',
              'events'        => 'Events & Photography',
              'general'       => 'General Services',
            ] as $val => $label)
              <option value="{{ $val }}" {{ old('service_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="kd-field">
        <label class="kd-label">Description</label>
        <textarea name="description" rows="5" class="kd-textarea @error('description') is-invalid @enderror"
                  placeholder="Describe what you offer, your experience, and what makes you stand out…" maxlength="5000">{{ old('description') }}</textarea>
        @error('description')<div class="kd-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="kd-form-section">
      <div class="kd-form-section-title">Pricing</div>
      <div class="kd-field-row">
        <div class="kd-field">
          <label class="kd-label">Pricing Model <span class="kd-req">*</span></label>
          <select name="pricing_model" class="kd-select" id="pricingModel">
            <option value="negotiable" {{ old('pricing_model','negotiable') === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
            <option value="fixed"      {{ old('pricing_model') === 'fixed'      ? 'selected' : '' }}>Fixed Price</option>
            <option value="hourly"     {{ old('pricing_model') === 'hourly'     ? 'selected' : '' }}>Per Hour</option>
            <option value="free_quote" {{ old('pricing_model') === 'free_quote' ? 'selected' : '' }}>Free Quote</option>
          </select>
        </div>
        <div class="kd-field" id="priceField">
          <label class="kd-label">Price (LKR)</label>
          <input type="number" name="price" value="{{ old('price') }}" class="kd-input" min="0" step="0.01" placeholder="0.00">
        </div>
      </div>
    </div>

    <div class="kd-form-section">
      <div class="kd-form-section-title">Location & Coverage</div>
      <div class="kd-field-row">
        <div class="kd-field">
          <label class="kd-label">Your Location <span class="kd-req">*</span></label>
          <input type="text" name="location" value="{{ old('location','Kegalle') }}" class="kd-input" required maxlength="100">
        </div>
        <div class="kd-field">
          <label class="kd-label">Years of Experience</label>
          <input type="number" name="experience_years" value="{{ old('experience_years',0) }}" class="kd-input" min="0" max="60">
        </div>
      </div>
      <div class="kd-field">
        <label class="kd-label">Areas Covered</label>
        <input type="text" name="areas_covered" value="{{ old('areas_covered') }}" class="kd-input"
               placeholder="e.g. Kegalle, Rambukkana, Mawanella" maxlength="500">
      </div>
    </div>

    <div class="kd-form-section">
      <div class="kd-form-section-title">Contact Details</div>
      <div class="kd-field-row">
        <div class="kd-field">
          <label class="kd-label">Phone</label>
          <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" class="kd-input" maxlength="20">
        </div>
        <div class="kd-field">
          <label class="kd-label">WhatsApp</label>
          <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" class="kd-input" maxlength="20">
        </div>
      </div>
      <div class="kd-field">
        <label class="kd-label">Email</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="kd-input" maxlength="100">
      </div>
    </div>

    <div class="kd-form-section">
      <div class="kd-form-section-title">Photo</div>
      <div class="kd-field">
        <label class="kd-label">Service Image (optional)</label>
        <input type="file" name="image" class="kd-file-input" accept="image/*">
        <div class="kd-field-hint">JPG, PNG or WebP · max 4 MB</div>
      </div>
    </div>

    <div class="kd-form-actions">
      <a href="{{ route('dashboard.services.index') }}" class="kd-btn kd-btn-outline">Cancel</a>
      <button type="submit" class="kd-btn kd-btn-primary">Submit for Approval</button>
    </div>
  </form>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var sel = document.getElementById('pricingModel');
  var pf  = document.getElementById('priceField');
  function toggle(){ pf.style.display = sel.value === 'free_quote' ? 'none' : ''; }
  sel.addEventListener('change', toggle);
  toggle();
})();
</script>
@endpush
@endsection
