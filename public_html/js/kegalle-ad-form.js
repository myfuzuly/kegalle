/* Post-ad form helpers: sticky progress from the checklist, inline length feedback, local draft.
   Drafts keep text only — photos can't be stored by the browser. */
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
  function el(tag, cls, text) { var n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; }
  function quill() { return window._quillDesc || null; }
  function descText() { var q = quill(); return q ? q.getText().trim() : ''; }

  /* ── 1. Sticky progress bar driven by the sidebar checklist ── */
  var items = document.querySelectorAll('#alcChecklist .alc-chk');
  if (items.length) {
    var bar = el('div', 'kaf-progress');
    bar.setAttribute('role', 'status');
    var track = el('div', 'kaf-progress-track'), fill = el('div', 'kaf-progress-fill'), label = el('span', 'kaf-progress-label');
    track.appendChild(fill); bar.appendChild(track); bar.appendChild(label);
    form.insertBefore(bar, form.firstChild);
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
  }

  /* ── 2. Inline feedback: counters + description minimum ── */
  var title = document.getElementById('aiTitleField');
  if (title) {
    var tCount = el('div', 'kaf-hint');
    title.insertAdjacentElement('afterend', tCount);
    var tUpd = function () {
      var n = title.value.trim().length, max = title.maxLength > 0 ? title.maxLength : 180;
      tCount.textContent = n < 10 ? n + '/' + max + ' · a clear title of 10+ characters gets more replies' : n + '/' + max;
      tCount.classList.toggle('is-warn', n > 0 && n < 10);
    };
    title.addEventListener('input', tUpd); tUpd();
  }

  var editor = document.getElementById('descEditor');
  var dHint = null;
  if (editor) {
    dHint = el('div', 'kaf-hint');
    dHint.id = 'kafDescHint';
    dHint.setAttribute('aria-live', 'polite');
    editor.insertAdjacentElement('afterend', dHint);
  }
  var descErrorShown = false;
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
      (editor || dHint).scrollIntoView({ behavior: 'smooth', block: 'center' });
      var q = quill(); if (q) q.focus();
      return;
    }
    store.clear();
  }, true);

  /* ── 3. Local draft (title, description, price, contact) ── */
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
    var d = store.get();
    var titleEmpty = !field('title') || !field('title').value.trim();
    if (!d || !titleEmpty || Date.now() - d.at > 14 * 864e5) return;
    var box = el('div', 'kaf-draft');
    box.setAttribute('role', 'region'); box.setAttribute('aria-label', 'Unsaved draft');
    var mins = Math.round((Date.now() - d.at) / 60000);
    var when = mins < 1 ? 'just now' : mins < 60 ? mins + ' min ago' : mins < 1440 ? Math.round(mins / 60) + ' h ago' : Math.round(mins / 1440) + ' days ago';
    box.appendChild(el('span', 'kaf-draft-text', 'You have an unsaved ad (' + when + ')' + (d.f.title ? ': “' + d.f.title.slice(0, 40) + '”' : '') + '. Photos need to be added again.'));
    var yes = el('button', 'kaf-draft-btn kaf-draft-btn--primary', 'Restore draft'); yes.type = 'button';
    var no = el('button', 'kaf-draft-btn', 'Discard'); no.type = 'button';
    box.appendChild(yes); box.appendChild(no);
    form.insertBefore(box, form.firstChild);
    yes.addEventListener('click', function () {
      Object.keys(d.f).forEach(function (n) { var i = field(n); if (i) { i.value = d.f[n]; i.dispatchEvent(new Event('input', { bubbles: true })); } });
      var q = quill(); if (q && d.desc) q.root.innerHTML = d.desc;
      box.remove();
    });
    no.addEventListener('click', function () { store.clear(); box.remove(); });
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
