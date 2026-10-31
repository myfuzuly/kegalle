/* Kegalle admin shell & shared behaviours (CSP-safe: no inline handlers needed). */
(function () {
  'use strict';
  var doc = document, root = doc.documentElement;
  var $ = function (s, c) { return (c || doc).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); };

  /* ── Snoozable notices: <div id="x" data-snooze="7" hidden> … <button data-snooze-close> ── */
  $$('[data-snooze]').forEach(function (el) {
    var key = 'k_snooze_' + el.id, until = 0;
    try { until = +localStorage.getItem(key) || 0; } catch (e) {}
    if (Date.now() < until) return;
    el.hidden = false;
    var x = $('[data-snooze-close]', el);
    if (x) x.addEventListener('click', function () {
      el.hidden = true;
      try { localStorage.setItem(key, Date.now() + el.getAttribute('data-snooze') * 864e5); } catch (e) {}
    });
  });
  var mq = window.matchMedia('(max-width: 1023px)');

  /* ── Sidebar: mobile drawer + desktop collapse ─────────────────────────── */
  var sidebar = $('#kxSidebar'), overlay = $('#kxOverlay'), menuBtn = $('#kxMenuBtn');
  var lastFocus = null;
  function openDrawer() {
    if (!sidebar) return;
    lastFocus = doc.activeElement;
    sidebar.classList.add('is-open');
    overlay.hidden = false;
    menuBtn.setAttribute('aria-expanded', 'true');
    doc.body.style.overflow = 'hidden';
    var first = $('#kxNavFilter') || $('a', sidebar);
    if (first) first.focus();
  }
  function closeDrawer() {
    if (!sidebar || !sidebar.classList.contains('is-open')) return;
    sidebar.classList.remove('is-open');
    overlay.hidden = true;
    menuBtn.setAttribute('aria-expanded', 'false');
    doc.body.style.overflow = '';
    if (lastFocus) lastFocus.focus();
  }
  if (menuBtn) menuBtn.addEventListener('click', openDrawer);
  if (overlay) overlay.addEventListener('click', closeDrawer);
  var closeBtn = $('#kxDrawerClose');
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  mq.addEventListener && mq.addEventListener('change', function (e) { if (!e.matches) closeDrawer(); });
  if (sidebar) sidebar.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !sidebar.classList.contains('is-open')) return;
    var f = $$('a[href], button:not([disabled]), input', sidebar).filter(function (el) { return el.offsetParent !== null; });
    if (!f.length) return;
    if (e.shiftKey && doc.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
    else if (!e.shiftKey && doc.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
  });

  var collapseBtn = $('#kxCollapse');
  function syncCollapse() {
    var c = root.classList.contains('kx-collapsed');
    if (collapseBtn) {
      collapseBtn.setAttribute('aria-pressed', c ? 'true' : 'false');
      collapseBtn.title = c ? 'Expand sidebar' : 'Collapse sidebar';
      var t = $('.kx-nav-text', collapseBtn); if (t) t.textContent = c ? 'Expand' : 'Collapse';
    }
  }
  if (collapseBtn) collapseBtn.addEventListener('click', function () {
    root.classList.toggle('kx-collapsed');
    try { localStorage.setItem('kx_sidebar', root.classList.contains('kx-collapsed') ? 'collapsed' : 'open'); } catch (e) {}
    syncCollapse();
  });
  syncCollapse();

  // Keep the active link visible inside a long sidebar.
  var active = $('.kx-nav-link.is-active');
  if (active && active.scrollIntoView) active.scrollIntoView({ block: 'nearest' });

  /* ── Menu filter ───────────────────────────────────────────────────────── */
  var filter = $('#kxNavFilter');
  if (filter) {
    filter.addEventListener('input', function () {
      var q = filter.value.trim().toLowerCase(), any = false;
      $$('.kx-nav-group').forEach(function (g) {
        var shown = 0;
        $$('li', g).forEach(function (li) {
          var hit = !q || (($('a', li).dataset.label || '').indexOf(q) !== -1);
          li.hidden = !hit; if (hit) shown++;
        });
        g.hidden = shown === 0; if (shown) any = true;
      });
      $('#kxNavEmpty').hidden = any;
    });
    filter.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { var l = $$('.kx-nav li:not([hidden]) a')[0]; if (l) { e.preventDefault(); l.click(); } }
    });
  }

  /* ── Popovers (notifications, user menu) ───────────────────────────────── */
  $$('.kx-pop').forEach(function (pop) {
    var btn = $('button[aria-haspopup]', pop), panel = $('.kx-pop-panel', pop);
    if (!btn || !panel) return;
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = panel.hidden;
      $$('.kx-pop-panel').forEach(function (p) { p.hidden = true; });
      $$('.kx-pop button[aria-haspopup]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
      panel.hidden = !open; btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
  doc.addEventListener('click', function (e) {
    if (!e.target.closest('.kx-pop')) {
      $$('.kx-pop-panel').forEach(function (p) { p.hidden = true; });
      $$('.kx-pop button[aria-haspopup]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
    }
  });

  /* ── Global search scope + "/" shortcut ────────────────────────────────── */
  var search = $('#kxSearch'), scope = $('#kxSearchScope'), sInput = $('#kxSearchInput');
  if (search && scope && sInput) {
    scope.addEventListener('change', function () {
      search.action = '/admin/' + scope.value;
      sInput.name = scope.value === 'listings' ? 'search' : 'q';
      sInput.focus();
    });
  }
  doc.addEventListener('keydown', function (e) {
    var t = doc.activeElement && doc.activeElement.tagName;
    if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(t) && !(doc.activeElement && doc.activeElement.isContentEditable) && sInput) {
      e.preventDefault(); sInput.focus(); sInput.select();
    }
    if (e.key === 'Escape') {
      closeDrawer();
      $$('.kx-pop-panel').forEach(function (p) { p.hidden = true; });
      if (doc.activeElement === sInput) sInput.blur();
    }
  });

  /* ── Alerts ────────────────────────────────────────────────────────────── */
  $$('.kx-alert-x').forEach(function (b) { b.addEventListener('click', function () { b.closest('.kx-alert').remove(); }); });
  $$('.kx-alert-success').forEach(function (a) { setTimeout(function () { a.classList.add('is-leaving'); setTimeout(function () { a.remove(); }, 300); }, 6000); });

  /* ── Confirm dialog for destructive actions ([data-confirm]) ───────────── */
  var dlg = $('#kxConfirm');
  function askConfirm(message, okLabel) {
    return new Promise(function (resolve) {
      if (!dlg) { resolve(window.confirm(message)); return; }
      $('#kxConfirmMsg', dlg).textContent = message;
      var ok = $('[data-kx-ok]', dlg), cancel = $('[data-kx-cancel]', dlg), prev = doc.activeElement;
      ok.textContent = okLabel || 'Confirm';
      dlg.hidden = false; cancel.focus();
      function done(v) { dlg.hidden = true; ok.removeEventListener('click', yes); cancel.removeEventListener('click', no); dlg.removeEventListener('keydown', key); if (prev) prev.focus(); resolve(v); }
      function yes() { done(true); } function no() { done(false); }
      function key(e) {
        if (e.key === 'Escape') no();
        if (e.key === 'Tab') { e.preventDefault(); (doc.activeElement === ok ? cancel : ok).focus(); }
      }
      ok.addEventListener('click', yes); cancel.addEventListener('click', no); dlg.addEventListener('keydown', key);
    });
  }
  function okLabelFor(msg) {
    var m = (msg || '').toLowerCase();
    if (m.indexOf('delete') !== -1 || m.indexOf('remove') !== -1) return 'Delete';
    if (m.indexOf('reject') !== -1) return 'Reject';
    if (m.indexOf('suspend') !== -1) return 'Suspend';
    return 'Confirm';
  }
  // Forms: data-confirm on the form, or a DELETE form without one gets a default prompt.
  doc.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement) || form.dataset.kxConfirmed === '1') return;
    var msg = form.getAttribute('data-confirm');
    var methodInput = form.querySelector('input[name="_method"]');
    if (!msg && methodInput && methodInput.value.toUpperCase() === 'DELETE') msg = 'Delete this item? This cannot be undone.';
    if (!msg) return;
    e.preventDefault(); e.stopImmediatePropagation();
    var submitter = e.submitter;
    askConfirm(msg, okLabelFor(msg)).then(function (yes) {
      if (!yes) return;
      form.dataset.kxConfirmed = '1';
      if (form.requestSubmit) form.requestSubmit(submitter && submitter.form === form ? submitter : undefined); else form.submit();
    });
  }, true);
  // Links/buttons with data-confirm (outside a confirming form).
  doc.addEventListener('click', function (e) {
    var el = e.target.closest('a[data-confirm], button[data-confirm]:not([type="submit"])');
    if (!el || el.dataset.kxConfirmed === '1') return;
    e.preventDefault();
    var msg = el.getAttribute('data-confirm');
    askConfirm(msg, okLabelFor(msg)).then(function (yes) {
      if (!yes) return;
      el.dataset.kxConfirmed = '1'; el.click(); delete el.dataset.kxConfirmed;
    });
  }, true);

  /* ── Auto-submit & named onchange hooks (replaces inline handlers) ─────── */
  doc.addEventListener('change', function (e) {
    var el = e.target;
    if (el.hasAttribute && el.hasAttribute('data-autosubmit') && el.form) {
      if (el.form.requestSubmit) el.form.requestSubmit(); else el.form.submit();
    }
    var fn = el.getAttribute && el.getAttribute('data-onchange');
    if (fn && typeof window[fn] === 'function') window[fn](el.value);
  });

  /* ── Inline validation for admin forms ─────────────────────────────────── */
  function fieldWrap(inp) { return inp.closest('.ka-field, .kaa-field, .set-field, label') || inp.parentElement; }
  function clearErr(inp) {
    var w = fieldWrap(inp); if (!w) return;
    w.classList.remove('ka-field-error'); inp.removeAttribute('aria-invalid');
    var m = w.querySelector('.ka-field-error-msg'); if (m) m.remove();
  }
  function setErr(inp, text) {
    var w = fieldWrap(inp); w.classList.add('ka-field-error'); inp.setAttribute('aria-invalid', 'true');
    if (w.querySelector('.ka-field-error-msg')) return;
    var m = doc.createElement('small'); m.className = 'ka-field-error-msg'; m.id = (inp.id || inp.name || 'f') + '-err'; m.textContent = text;
    inp.setAttribute('aria-describedby', m.id);
    (inp.nextElementSibling && inp.nextElementSibling.classList.contains('select2') ? inp.nextElementSibling : inp).insertAdjacentElement('afterend', m);
  }
  doc.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('.ka-premium-form, .kd-form, .kx-form, form[data-validate]')) return;
    var firstBad = null;
    $$('[required]', form).forEach(function (inp) {
      clearErr(inp);
      if (inp.disabled || inp.type === 'hidden') return;
      var v = (inp.type === 'checkbox' || inp.type === 'radio') ? (form.querySelector('[name="' + inp.name + '"]:checked') ? 'x' : '') : (inp.value || '').trim();
      if (!v) { setErr(inp, (fieldLabel(inp) || 'This field') + ' is required.'); firstBad = firstBad || inp; }
    });
    $$('input[type="email"]', form).forEach(function (inp) {
      var v = (inp.value || '').trim();
      if (v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { setErr(inp, 'Enter a valid email address, like name@example.com.'); firstBad = firstBad || inp; }
    });
    if (firstBad) {
      e.preventDefault(); e.stopImmediatePropagation();
      firstBad.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
      firstBad.focus({ preventScroll: true });
    }
  });
  function fieldLabel(inp) {
    var l = inp.id && doc.querySelector('label[for="' + inp.id + '"]');
    l = l || (inp.closest('label')) || (fieldWrap(inp) && fieldWrap(inp).querySelector('label, .ka-label, .kaa-label, .set-label'));
    return l ? l.textContent.replace(/[*:]/g, '').replace(/\(optional\)/i, '').trim() : '';
  }
  doc.addEventListener('input', function (e) { if (e.target.getAttribute && e.target.getAttribute('aria-invalid')) clearErr(e.target); });

  /* ── Prevent duplicate submissions (runs after validation/confirm) ─────── */
  doc.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement) || e.defaultPrevented || form.method.toLowerCase() === 'get' || form.target === '_blank') return;
    setTimeout(function () {
      if (e.defaultPrevented) return;
      var btn = e.submitter || form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');
      if (form.dataset.kxBusy === '1') return;
      form.dataset.kxBusy = '1';
      if (btn) { btn.classList.add('ka-loading'); btn.setAttribute('aria-busy', 'true'); btn.disabled = true; }
      // Re-enable if the page stays (e.g. file download or bfcache restore).
      setTimeout(function () { delete form.dataset.kxBusy; if (btn) { btn.disabled = false; btn.classList.remove('ka-loading'); btn.removeAttribute('aria-busy'); } }, 8000);
    }, 0);
  });
  window.addEventListener('pageshow', function (ev) {
    if (!ev.persisted) return;
    $$('form[data-kx-busy]').forEach(function (f) { delete f.dataset.kxBusy; });
    $$('button.ka-loading').forEach(function (b) { b.disabled = false; b.classList.remove('ka-loading'); });
  });

  /* ── Tables: keyboard-scrollable wrappers + accessible icon actions ────── */
  $$('.sa-table-wrap, .ka-table-wrap, .kx-table-wrap').forEach(function (w) {
    if (!w.hasAttribute('tabindex')) w.setAttribute('tabindex', '0');
    if (!w.hasAttribute('role')) { w.setAttribute('role', 'region'); w.setAttribute('aria-label', 'Table, scroll horizontally for more'); }
  });
  $$('.sa-actions-inline a, .sa-actions-inline button, .ka-actions-inline a, .ka-actions-inline button').forEach(function (el) {
    var txt = (el.getAttribute('title') || el.textContent || '').trim();
    if (txt && !el.getAttribute('aria-label')) el.setAttribute('aria-label', txt);
  });
  // Wrap bare tables so wide content scrolls inside its card, never the page.
  $$('.kx-content table').forEach(function (t) {
    if (t.closest('.sa-table-wrap, .ka-table-wrap, .kx-table-wrap, .table-responsive')) return;
    var w = doc.createElement('div'); w.className = 'kx-table-wrap'; w.setAttribute('tabindex', '0');
    t.parentNode.insertBefore(w, t); w.appendChild(t);
  });

  // Row actions: keep the first two visible, tuck the rest into a "More" menu.
  $$('.kx-content .sa-actions-inline, .kx-content .ka-actions-inline').forEach(function (cell, n) {
    var box = cell;
    if (cell.tagName === 'TD') {
      box = doc.createElement('div'); box.className = 'kx-actions';
      while (cell.firstChild) box.appendChild(cell.firstChild);
      cell.appendChild(box); cell.classList.add('kx-actions-cell');
    } else { cell.classList.add('kx-actions'); }
    var items = Array.prototype.filter.call(box.children, function (el) { return el.matches('a, button, form'); });
    if (items.length <= 3) return;
    var more = doc.createElement('div'); more.className = 'kx-pop kx-more';
    var id = 'kxMore' + n;
    more.innerHTML = '<button type="button" class="kx-more-btn" aria-haspopup="true" aria-expanded="false" aria-controls="' + id + '">More<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></button><div class="kx-pop-panel kx-more-panel" id="' + id + '" role="menu" hidden></div>';
    var panel = more.lastChild;
    items.slice(2).forEach(function (el) { panel.appendChild(el); });
    box.appendChild(more);
    var btn = more.firstChild;
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = panel.hidden;
      $$('.kx-pop-panel').forEach(function (p) { p.hidden = true; });
      panel.hidden = !open; btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) {
        var r = btn.getBoundingClientRect();
        more.classList.toggle('kx-up', r.bottom + 220 > innerHeight);
        var f = $('a, button', panel); if (f) f.focus();
      }
    });
  });

  // Label each cell with its column heading so tables can stack into cards on phones.
  $$('.kx-content table').forEach(function (t) {
    if (t.classList.contains('kx-no-stack')) return;
    var heads = $$('thead th', t).map(function (th) { return th.textContent.trim(); });
    if (!heads.length) return;
    t.classList.add('kx-stack');
    $$('tbody tr', t).forEach(function (tr) {
      var i = 0;
      Array.prototype.forEach.call(tr.children, function (td) {
        var span = +td.getAttribute('colspan') || 1;
        if (!td.hasAttribute('data-label') && heads[i]) td.setAttribute('data-label', heads[i]);
        if (span > 1) td.classList.add('kx-span');
        i += span;
      });
    });
  });

  /* ── Searchable selects: only where they help (long option lists) ─────── */
  function enhanceSelects() {
    if (!(window.jQuery && jQuery.fn && jQuery.fn.select2)) return;
    jQuery('.kx-content select').each(function () {
      var $s = jQuery(this);
      if ($s.hasClass('select2-hidden-accessible') || $s.hasClass('no-select2') || $s.closest('.kaa-filter-bar, .kn-filter-wrap, .kn-filterbar').length) return;
      if (this.options.length < 9 && !$s.data('select2-force')) return;
      $s.select2({ width: '100%', minimumResultsForSearch: 8, placeholder: $s.data('placeholder') || $s.attr('placeholder') || 'Select an option', allowClear: !!$s.data('placeholder') });
    });
  }
  window.addEventListener('load', enhanceSelects);
})();

/* ── Bulk selection ([data-bulk-bar] form + .kx-row-check rows joined via form="…") ── */
(function () {
  var bar = document.querySelector('[data-bulk-bar]');
  if (!bar) return;
  var rows = function () { return document.querySelectorAll('input.kx-row-check[form="' + bar.id + '"]'); };
  var all = document.querySelector('[data-bulk-all]');
  var count = bar.querySelector('[data-bulk-count]');
  function sync() {
    var n = 0, total = 0;
    rows().forEach(function (c) { total++; if (c.checked) n++; });
    // Only write on change: the body MutationObserver below would otherwise re-trigger sync forever
    var txt = n + ' selected';
    if (count.textContent !== txt) count.textContent = txt;
    if (bar.hidden !== (n === 0)) bar.hidden = n === 0;
    if (all) { all.checked = total > 0 && n === total; all.indeterminate = n > 0 && n < total; }
  }
  // Delegated: the table body is replaced by AJAX when filters change.
  document.addEventListener('change', function (e) {
    if (e.target === all) rows().forEach(function (c) { c.checked = all.checked; });
    if (e.target === all || (e.target.classList && e.target.classList.contains('kx-row-check'))) sync();
  });
  var clear = bar.querySelector('[data-bulk-clear]');
  if (clear) clear.addEventListener('click', function () { rows().forEach(function (c) { c.checked = false; }); sync(); });
  // Watch the table that holds the select-all box; AJAX filters swap its rows
  var body = (all && all.closest('table') && all.closest('table').tBodies[0]) || document.body;
  new MutationObserver(sync).observe(body, { childList: true, subtree: body === document.body });
  sync();
})();

/* ── Click-to-sort table headers (sorts the rows on the current page) ── */
(function () {
  function cellValue(tr, i) {
    var td = tr.children[i]; if (!td) return '';
    var t = td.textContent.replace(/\s+/g, ' ').trim();
    var num = t.replace(/^LKR\s*/i, '').replace(/,/g, '');
    return /^-?\d+(\.\d+)?$/.test(num) ? parseFloat(num) : t.toLowerCase();
  }
  document.querySelectorAll('.kx-body table.sa-table').forEach(function (table) {
    var tbody = table.tBodies[0]; if (!tbody) return;
    Array.prototype.forEach.call(table.querySelectorAll('thead th'), function (th, i) {
      var label = th.textContent.trim();
      if (!label || /^(image|logo|icon|action|actions)$/i.test(label) || th.querySelector('input')) return;
      var btn = document.createElement('button');
      btn.type = 'button'; btn.className = 'kx-sort'; btn.textContent = label;
      btn.setAttribute('aria-label', 'Sort by ' + label);
      th.textContent = ''; th.appendChild(btn);
      btn.addEventListener('click', function () {
        var asc = th.getAttribute('aria-sort') !== 'ascending';
        table.querySelectorAll('thead th[aria-sort]').forEach(function (o) { o.removeAttribute('aria-sort'); });
        th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
        var trs = Array.prototype.filter.call(tbody.rows, function (r) { return r.children.length > 1; });
        trs.sort(function (a, b) {
          var x = cellValue(a, i), y = cellValue(b, i);
          var c = typeof x === 'number' && typeof y === 'number' ? x - y : String(x).localeCompare(String(y));
          return asc ? c : -c;
        });
        trs.forEach(function (r) { tbody.appendChild(r); });
      });
    });
  });
})();

/* ── Loading state: dim tables while an AJAX filter reloads this same page ── */
(function () {
  if (!window.fetch || !document.querySelector('.sa-table-wrap')) return;
  var orig = window.fetch, inFlight = 0;
  function setBusy(on) {
    document.querySelectorAll('.sa-table-wrap').forEach(function (w) {
      w.classList.toggle('kx-loading', on);
      w.setAttribute('aria-busy', on ? 'true' : 'false');
    });
  }
  window.fetch = function (input, init) {
    var url = typeof input === 'string' ? input : (input && input.url) || '';
    var path; try { path = new URL(url, location.href).pathname; } catch (e) { path = ''; }
    if (path !== location.pathname) return orig.apply(this, arguments);
    inFlight++; setBusy(true);
    return orig.apply(this, arguments).finally(function () { if (--inFlight === 0) setBusy(false); });
  };
})();
