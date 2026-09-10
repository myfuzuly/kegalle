@extends('layouts.dashboard')
@section('title','Upgrade to ' . ucfirst($plan))
@section('eyebrow','Membership')
@section('heading','Upgrade to ' . $meta['name'])
@section('banner_sub', 'Complete your bank transfer and submit your payment slip to activate your plan.')

@section('content')

@php
  $planFeatures = [
    'silver'   => ['15 active listings','Store analytics dashboard','Priority listing review','Remove ads from your store','Email support'],
    'gold'     => ['50 active listings','Featured badge on store','Homepage spotlight (monthly)','Bulk import listings','Dedicated support','Priority search placement'],
    'platinum' => ['200 active listings','Top-of-search placement','Verified seller badge','Featured on homepage carousel','Early access to new features','Dedicated account manager'],
  ];
  $features   = $planFeatures[$plan] ?? [];
  $tierColor  = $plan==='gold' ? '#b45309' : ($plan==='platinum' ? '#6d28d9' : '#475569');
  $tierBg     = $plan==='gold' ? 'linear-gradient(135deg,#fffbeb,#fef3c7)' : ($plan==='platinum' ? 'linear-gradient(135deg,#f5f3ff,#ede9fe)' : 'linear-gradient(135deg,#f8fafc,#f1f5f9)');
  $tierBtnCls = $plan==='gold' ? 'upg-submit-gold' : ($plan==='platinum' ? 'upg-submit-platinum' : 'upg-submit-silver');
  $tierIcon   = $plan==='gold' ? '🥇' : ($plan==='platinum' ? '💎' : '🥈');
  $tierBorder = $plan==='gold' ? '#d97706' : ($plan==='platinum' ? '#7c3aed' : '#94a3b8');
@endphp

@if($errors->any())
<div class="prf-alert prf-alert-error" style="margin-bottom:20px">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  {{ $errors->first() }}
</div>
@endif

@if($pending)
<div class="prf-alert prf-alert-warning" style="margin-bottom:20px">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  You already have a pending upgrade request for the <strong>{{ $meta['name'] }}</strong> plan. We'll activate it within 24 hours.
</div>
@endif

{{-- Step guide --}}
<div class="upg-steps">
  <div class="upg-step upg-step-done">
    <div class="upg-step-num">✓</div>
    <div class="upg-step-label">Choose Plan</div>
  </div>
  <div class="upg-step-line upg-step-line-done"></div>
  <div class="upg-step upg-step-active">
    <div class="upg-step-num">2</div>
    <div class="upg-step-label">Transfer &amp; Upload</div>
  </div>
  <div class="upg-step-line"></div>
  <div class="upg-step">
    <div class="upg-step-num">3</div>
    <div class="upg-step-label">Admin Approval</div>
  </div>
</div>

<div class="upg-grid">

  {{-- LEFT COLUMN --}}
  <div class="upg-col">

    {{-- Plan summary --}}
    <div class="upg-card upg-plan-hero" style="border-color:{{ $tierBorder }};background:{{ $tierBg }}">
      <div class="upg-plan-hero-top">
        <div class="upg-plan-icon" style="background:rgba(255,255,255,.75)">{{ $tierIcon }}</div>
        <div>
          <div class="upg-plan-name" style="color:{{ $tierColor }}">{{ $meta['name'] }} Plan</div>
          <div class="upg-plan-sub">30 days · bank transfer</div>
        </div>
        <div class="upg-plan-amount-pill" style="background:{{ $tierColor }};color:#fff">
          LKR {{ number_format($meta['price']) }}<span style="font-size:11px;font-weight:500;opacity:.8">.00</span>
        </div>
      </div>

      @if(count($features))
      <div class="upg-plan-feats">
        <div class="upg-plan-feats-label">What you're unlocking</div>
        @foreach($features as $feat)
        <div class="upg-plan-feat-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $tierColor }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ $feat }}
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Bank details --}}
    <div class="upg-card upg-bank-card">
      <div class="upg-bank-header">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        Bank Transfer Details
      </div>

      {{-- Bank tabs --}}
      <div class="upg-bank-tabs">
        <button type="button" id="btab1" class="upg-btab upg-btab-active">Bank of Ceylon</button>
        <button type="button" id="btab2" class="upg-btab">Pan Asia Bank</button>
      </div>

      {{-- Bank 1 --}}
      <div id="bdetails1" class="upg-bank-rows">
        @foreach([
          ['Bank',         'Bank of Ceylon'],
          ['Account Name', 'Kegalle Marketplace'],
          ['Account No.',  '0000 0000 0000', true],
          ['Branch',       'Kegalle'],
        ] as $row)
        <div class="upg-brow">
          <span class="upg-brow-label">{{ $row[0] }}</span>
          <span class="upg-brow-val">
            {{ $row[1] }}
            @if(!empty($row[2]))
            <button type="button" class="upg-copy" data-copy-text="{{ $row[1] }}" title="Copy">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
            @endif
          </span>
        </div>
        @endforeach
      </div>

      {{-- Bank 2 --}}
      <div id="bdetails2" class="upg-bank-rows" style="display:none">
        @foreach([
          ['Bank',         'Pan Asia Bank'],
          ['Account Name', 'Kegalle Marketplace'],
          ['Account No.',  '0000 0000 0000', true],
          ['Branch',       'Kegalle'],
        ] as $row)
        <div class="upg-brow">
          <span class="upg-brow-label">{{ $row[0] }}</span>
          <span class="upg-brow-val">
            {{ $row[1] }}
            @if(!empty($row[2]))
            <button type="button" class="upg-copy" data-copy-text="{{ $row[1] }}" title="Copy">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
            @endif
          </span>
        </div>
        @endforeach
      </div>

      {{-- Reference box --}}
      <div class="upg-ref-box">
        <div class="upg-ref-label">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Include this reference in your transfer description
        </div>
        <div class="upg-ref-code-row">
          <span class="upg-ref-code" id="ref-code">{{ $reference }}</span>
          <button type="button" class="upg-copy upg-copy-ref" data-copy-text="{{ $reference }}" title="Copy reference">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          </button>
        </div>
      </div>
    </div>

    <a href="/dashboard/membership" class="upg-back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Back to plans
    </a>
  </div>

  {{-- RIGHT COLUMN --}}
  <div class="upg-col">
    <div class="upg-card">
      <div class="upg-section-label" style="margin-bottom:4px">Upload Payment Slip</div>
      <p class="upg-upload-hint">After making the transfer, upload your bank receipt or a screenshot of the confirmation.</p>

      <form method="POST" action="/dashboard/membership/upgrade/{{ $plan }}" enctype="multipart/form-data">
        @csrf

        {{-- Drop zone --}}
        <div class="upg-drop-wrap">
          <label for="slip-input" id="slip-drop" class="upg-drop">
            <div class="upg-drop-icon" id="slip-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div id="slip-label" class="upg-drop-text">
              <strong>Tap to select or drag &amp; drop</strong><br>
              <span>JPG, PNG or PDF &middot; max 5 MB</span>
            </div>
          </label>
          <input type="file" id="slip-input" name="slip" accept=".jpg,.jpeg,.png,.pdf" required class="hidden">
        </div>

        {{-- Confirm checkbox --}}
        <label class="upg-confirm">
          <input type="checkbox" name="confirmed" value="1" required>
          <span>
            I confirm I have transferred <strong>LKR {{ number_format($meta['price']) }}.00</strong>
            to the above account and included reference <strong class="upg-ref-inline">{{ $reference }}</strong>.
          </span>
        </label>

        @if(!$pending)
        <button type="submit" class="upg-submit {{ $tierBtnCls }}" id="upg-submit-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Activate {{ $meta['name'] }} Plan
        </button>
        <div class="upg-trust-row">
          <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Secure offline transfer</span>
          <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Activated within 24 hrs</span>
          <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 11.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg> Email support</span>
        </div>
        @else
        <div class="upg-pending-note">Request already submitted — pending admin review</div>
        @endif

        <div class="upg-footer-note">
          Questions? <a href="mailto:support@kegalle.com">support@kegalle.com</a>
        </div>
      </form>
    </div>

    {{-- Timeline card --}}
    <div class="upg-card upg-timeline-card">
      <div class="upg-section-label">What happens next?</div>
      <div class="upg-timeline">
        <div class="upg-tl-item upg-tl-done">
          <div class="upg-tl-dot"></div>
          <div class="upg-tl-text"><strong>Transfer payment</strong><br><span>Send LKR {{ number_format($meta['price']) }} to either bank above</span></div>
        </div>
        <div class="upg-tl-item">
          <div class="upg-tl-dot"></div>
          <div class="upg-tl-text"><strong>Upload your slip</strong><br><span>Submit this form with your receipt</span></div>
        </div>
        <div class="upg-tl-item">
          <div class="upg-tl-dot"></div>
          <div class="upg-tl-text"><strong>Admin verifies</strong><br><span>Usually within a few hours</span></div>
        </div>
        <div class="upg-tl-item">
          <div class="upg-tl-dot" style="background:{{ $tierColor }};border-color:{{ $tierColor }}"></div>
          <div class="upg-tl-text"><strong style="color:{{ $tierColor }}">{{ $meta['name'] }} plan activated! 🎉</strong><br><span>Your new benefits go live immediately</span></div>
        </div>
      </div>
    </div>
  </div>

</div>



<script nonce="{{ $cspNonce ?? '' }}">
function showBank(n) {
  document.getElementById('bdetails1').style.display = n===1?'block':'none';
  document.getElementById('bdetails2').style.display = n===2?'block':'none';
  document.getElementById('btab1').classList.toggle('upg-btab-active', n===1);
  document.getElementById('btab2').classList.toggle('upg-btab-active', n===2);
}
function onFileChange(inp) {
  if (!inp.files[0]) return;
  const f = inp.files[0];
  document.getElementById('slip-label').innerHTML =
    '<strong class="text-green900">'+f.name+'</strong><br><span>'+Math.round(f.size/1024)+' KB — ready to upload</span>';
  document.getElementById('slip-icon').innerHTML =
    '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
  document.getElementById('slip-drop').classList.add('upg-drop-success');
}
function handleDrop(e) {
  e.preventDefault();
  const f = e.dataTransfer.files[0];
  if (!f) return;
  const inp = document.getElementById('slip-input');
  const dt = new DataTransfer(); dt.items.add(f); inp.files = dt.files;
  onFileChange(inp);
  document.getElementById('slip-drop').classList.remove('upg-drop-over');
}
document.querySelector('form') && document.querySelector('form').addEventListener('submit', function(){
  var btn = document.getElementById('upg-submit-btn');
  if(btn){ btn.disabled=true; btn.innerHTML='<svg class="anim-spin-upg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Submitting…'; }
});
function copyText(txt, btn) {
  navigator.clipboard.writeText(txt).then(function(){
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
    setTimeout(function(){ btn.innerHTML = orig; }, 1500);
  });
}
(function(){
  var b1=document.getElementById('btab1'), b2=document.getElementById('btab2');
  if(b1) b1.addEventListener('click',function(){ showBank(1); });
  if(b2) b2.addEventListener('click',function(){ showBank(2); });
})();
document.addEventListener('click',function(e){
  var btn=e.target.closest('[data-copy-text]');
  if(btn) copyText(btn.dataset.copyText, btn);
});
(function(){
  var drop=document.getElementById('slip-drop');
  if(!drop) return;
  drop.addEventListener('dragover',function(e){ e.preventDefault(); drop.classList.add('upg-drop-over'); });
  drop.addEventListener('dragleave',function(){ drop.classList.remove('upg-drop-over'); });
  drop.addEventListener('drop',function(e){ handleDrop(e); });
})();
(function(){
  var inp=document.getElementById('slip-input');
  if(inp) inp.addEventListener('change',function(){ onFileChange(this); });
})();
</script>

@endsection
