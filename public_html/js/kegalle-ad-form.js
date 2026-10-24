/* Post-ad form helpers. Markup lives in Blade components (x-form.progress, x-form.draft-banner,
   x-form.field-hint); this file only fills them in. Drafts keep text only — browsers can't store photos. */
(function () {
  var form = document.getElementById('alcForm');
  if (!form) return;

  var KEY = 'kg_ad_draft_v1';
  var DESC_MIN = 20; // matches StoreListingRequest 'description' => min:20
  var store = {
    get: function () { try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { return null; } },
    set: function (v) { try { localStorage.setItem(KEY, JSON.stringify(v)); } catch (e) {} },
    clear: function () { try { localStorage.removeItem(KEY); } catch (e) {} }
  };
  function quill() { return window._quillDesc || null; }
  function descText() { var q = quill(); return q ? q.getText().trim() : ''; }
  function hint(name) { return form.querySelector('[data-hint-for="' + name + '"]'); }

  /* ── 1. Progress bar (x-form.progress) driven by the sidebar checklist ── */
  var bar = form.querySelector('[data-form-progress]');
  var items = bar ? document.querySelectorAll(bar.getAttribute('data-form-progress') + ' .alc-chk') : [];
  if (bar && items.length) {
    var fill = bar.querySelector('[data-progress-fill]'), label = bar.querySelector('[data-progress-label]');
    var update = function () {
      var done = 0;
      items.forEach(function (i) { if (i.dataset.done === '1') done++; });
      fill.style.width = Math.round(done / items.length * 100) + '%';
      label.textContent = done === items.length ? 'Ready to submit' : done + ' of ' + items.length + ' steps done';
      bar.classList.toggle('is-complete', done === items.length);
    };
    var mo = new MutationObserver(update);
    items.forEach(function (i) { mo.observe(i, { attributes: true, attributeFilter: ['data-done'] }); });
    update();
  } else if (bar) {
    bar.hidden = true;
  }

  /* ── 2. Inline feedback (x-form.field-hint) ── */
  var title = form.querySelector('[name="title"]'), tHint = hint('title');
  if (title && tHint) {
    var tUpd = function () {
      var n = title.value.trim().length, max = title.maxLength > 0 ? title.maxLength : 180;
      tHint.textContent = n < 10 ? n + '/' + max + ' · a clear title of 10+ characters gets more replies' : n + '/' + max;
      tHint.classList.toggle('is-warn', n > 0 && n < 10);
    };
    title.addEventListener('input', tUpd); tUpd();
  }

  var dHint = hint('description'), descErrorShown = false;
  function dUpd(showError) {
    if (!dHint) return;
    var n = descText().length, short = n < DESC_MIN;
    if (showError) descErrorShown = true;
    if (!short) descErrorShown = false;
    dHint.textContent = short ? n + ' characters · at least ' + DESC_MIN + ' needed' : n + ' characters';
    dHint.classList.toggle('is-error', short && descErrorShown);
    dHint.classList.toggle('is-warn', short && !descErrorShown && n > 0);
  }

  form.addEventListener('submit', function (e) {
    if (quill() && descText().length < DESC_MIN) {
      // The server would reject it and the reload drops the chosen photos, so stop here instead.
      e.preventDefault(); e.stopImmediatePropagation();
      dUpd(true);
      if (dHint) dHint.scrollIntoView({ behavior: 'smooth', block: 'center' });
      quill().focus();
      return;
    }
    store.clear();
  }, true);

  /* ── 3. Local draft (x-form.draft-banner) ── */
  var FIELDS = ['title', 'price', 'contact_phone', 'contact_email', 'contact_whatsapp'];
  function field(name) { return form.querySelector('[name="' + name + '"]'); }
  function snapshot() {
    var d = { at: Date.now(), f: {} };
    FIELDS.forEach(function (n) { var i = field(n); if (i && i.value) d.f[n] = i.value; });
    var q = quill(); if (q && descText()) d.desc = q.root.innerHTML;
    return d;
  }
  var t = null;
  function scheduleSave() {
    clearTimeout(t);
    t = setTimeout(function () { var s = snapshot(); if (s.f.title || s.desc) store.set(s); }, 800);
  }
  form.addEventListener('input', scheduleSave);

  function offerRestore() {
    var box = form.querySelector('[data-draft-banner]'), d = store.get();
    var titleEmpty = !field('title') || !field('title').value.trim();
    if (!box || !d || !titleEmpty || Date.now() - d.at > 14 * 864e5) return;
    var mins = Math.round((Date.now() - d.at) / 60000);
    var when = mins < 1 ? 'just now' : mins < 60 ? mins + ' min ago' : mins < 1440 ? Math.round(mins / 60) + ' h ago' : Math.round(mins / 1440) + ' days ago';
    box.querySelector('[data-draft-text]').textContent =
      'You have an unsaved ad (' + when + ')' + (d.f.title ? ': “' + d.f.title.slice(0, 40) + '”' : '') + '. Photos need to be added again.';
    box.hidden = false;
    box.querySelector('[data-draft-restore]').addEventListener('click', function () {
      Object.keys(d.f).forEach(function (n) { var i = field(n); if (i) { i.value = d.f[n]; i.dispatchEvent(new Event('input', { bubbles: true })); } });
      var q = quill(); if (q && d.desc) q.root.innerHTML = d.desc;
      box.hidden = true;
    });
    box.querySelector('[data-draft-discard]').addEventListener('click', function () { store.clear(); box.hidden = true; });
  }

  // Quill is created on DOMContentLoaded by the page; wait for it before wiring description bits.
  var tries = 0, wait = setInterval(function () {
    if (quill() || ++tries > 40) {
      clearInterval(wait);
      var q = quill();
      if (q) q.on('text-change', function () { dUpd(false); scheduleSave(); });
      dUpd(false);
      offerRestore();
    }
  }, 150);
})();
