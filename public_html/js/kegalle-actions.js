/* Public-site behaviours wired by data attributes — the CSP blocks inline on*="" handlers.
   data-autosubmit            <select>/<input>: submit its form on change
   data-copy-url              button: copy the page URL, then show "Copied"
   data-call="fn" [data-arg]  click: window[fn](arg)            (fn must be a page global)
   data-onsubmit="fn"         <form>: window[fn](event) on submit
   data-oninput="fn"          <input>: window[fn](value) on input
   data-enter-click           Enter/Space acts as a click (role="button" elements)
   data-fallback="hide" | "fail:<ancestor selector>" | "show:<element id>" | "nophoto:<ancestor selector>"   <img>: what to do when it fails
   data-sort-param="sort"     <select>: reload the page with ?sort=<value> (keeps other filters, resets page)
   data-set-field="<form id>" <select name=x>: copy value into that form's field x, then submit it
   data-loading-submit        <form>: mark its submit button busy while the page loads
   data-require="#input" data-require-msg="..."  <form>: block submit while #input's value < 1, show msg */
(function () {
  var doc = document;
  function fn(name) { return name && typeof window[name] === 'function' ? window[name] : null; }

  doc.addEventListener('change', function (e) {
    var t = e.target;
    if (!t.closest) return;
    var el = t.closest('[data-autosubmit]');
    if (el && el.form) { el.form.submit(); return; }
    if (t.hasAttribute('data-sort-param')) {
      var u = new URL(location.href);
      u.searchParams.set(t.getAttribute('data-sort-param'), t.value);
      u.searchParams.delete('page');
      location.href = u.toString();
      return;
    }
    var target = t.getAttribute('data-set-field') && doc.getElementById(t.getAttribute('data-set-field'));
    if (target) { var f = target.elements[t.getAttribute('data-name') || t.name]; if (f) f.value = t.value; target.submit(); }
  });

  doc.addEventListener('input', function (e) {
    var el = e.target.closest && e.target.closest('[data-oninput]');
    var f = el && fn(el.getAttribute('data-oninput'));
    if (f) f(el.value);
  });

  doc.addEventListener('click', function (e) {
    var copy = e.target.closest('[data-copy-url]');
    if (copy && navigator.clipboard) {
      navigator.clipboard.writeText(location.href).then(function () {
        var label = copy.textContent; copy.textContent = 'Copied';
        setTimeout(function () { copy.textContent = label; }, 1500);
      });
      return;
    }
    var call = e.target.closest('[data-call]');
    var f = call && fn(call.getAttribute('data-call'));
    if (f) { e.preventDefault(); f(call.getAttribute('data-arg')); }
  });

  doc.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.getAttribute) return;
    var need = form.getAttribute('data-require');
    if (need) {
      var input = doc.querySelector(need), err = form.querySelector('.k-field-error');
      if (input && !(parseInt(input.value, 10) >= 1)) {
        e.preventDefault();
        if (!err) { err = doc.createElement('p'); err.className = 'k-field-error'; err.setAttribute('role', 'alert'); input.parentNode.appendChild(err); }
        err.textContent = form.getAttribute('data-require-msg') || 'Please complete this field.';
        return;
      }
      if (err) err.remove();
    }
    if (form.hasAttribute('data-loading-submit')) {
      var btn = form.querySelector('[type=submit]');
      if (btn) { btn.classList.add('k-loading'); btn.disabled = true; }
    }
    var f = fn(form.getAttribute('data-onsubmit'));
    if (f) f(e);
  });

  doc.addEventListener('keydown', function (e) {
    if ((e.key === 'Enter' || e.key === ' ') && e.target.matches && e.target.matches('[data-enter-click]')) {
      e.preventDefault(); e.target.click();
    }
  });

  // error events don't bubble, so listen in the capture phase; also covers images added later by JS
  doc.addEventListener('error', function (e) {
    var img = e.target;
    if (!img || img.tagName !== 'IMG' || !img.hasAttribute('data-fallback')) return;
    var how = img.getAttribute('data-fallback'), arg = how.slice(how.indexOf(':') + 1);
    if (how.indexOf('fail:') === 0) { var box = img.closest(arg); if (box) box.classList.add('k-img-failed'); img.remove(); }
    else if (how.indexOf('nophoto:') === 0) {
      var w = img.closest(arg);
      if (w) { w.classList.add('kpc-no-photo'); var p = w.querySelector('.kpc-ph-fb'); if (p) p.style.display = 'flex'; }  // CSSOM is CSP-safe
      img.remove();
    }
    else if (how.indexOf('show:') === 0) { img.style.display = 'none'; var ph = doc.getElementById(arg); if (ph) ph.style.display = 'flex'; }
    else img.style.display = 'none';
  }, true);
})();
