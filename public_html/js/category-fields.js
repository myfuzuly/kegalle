(function(){
'use strict';
if (!document.querySelector('link[href*="kegalle-main"]')) {
var s = document.createElement('style');
s.textContent = '.cf-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:18px;padding:20px;background:#f7f8fa;border-radius:12px;border:1px solid #e5e8ef}.cf-field-wrap{display:flex;flex-direction:column;gap:6px}.cf-field-wrap.cf-full-width{grid-column:1/-1}.cf-label{font-size:13px;font-weight:600;color:#0d1b2a}.cf-req{color:#d32f2f}.cf-input,.cf-select{width:100%;padding:10px 12px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:14px;color:#0d1b2a;background:#fff;transition:border-color .2s;box-sizing:border-box}.cf-input:focus,.cf-select:focus{outline:none;border-color:#43a047;box-shadow:0 0 0 3px rgba(27,94,32,.08)}.cf-checkbox-group{display:flex;flex-wrap:wrap;gap:8px}.cf-checkbox-label{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:1.5px solid #e5e8ef;background:#fff;font-size:13px;color:#374151;cursor:pointer;transition:all .18s;user-select:none;font-weight:500}.cf-checkbox-label:hover{border-color:#43a047;color:#1b5e20;background:#f0fdf4}.cf-checkbox-label input[type=checkbox]{position:absolute;opacity:0;pointer-events:none;width:0;height:0}.cf-checkbox-label.cf-checked{border-color:#1b5e20;background:#1b5e20;color:#fff;font-weight:700}.cf-pill-group{display:flex;flex-wrap:wrap;gap:6px}.cf-pill{display:inline-flex;align-items:center;justify-content:center;padding:5px 14px;border-radius:20px;border:1.5px solid #e5e8ef;background:#fff;font-size:13px;color:#374151;cursor:pointer;transition:all .18s;user-select:none;font-family:inherit;line-height:1.4;font-weight:500}.cf-pill:hover{border-color:#43a047;color:#1b5e20;background:#f0fdf4}.cf-pill.selected{border-color:#1b5e20;background:#1b5e20;color:#fff;font-weight:700}.cf-pill input{position:absolute;opacity:0;pointer-events:none}.cf-text-unit-wrap{display:flex;align-items:center;gap:0}.cf-text-unit-wrap .cf-input{border-radius:10px 0 0 10px;border-right:none;width:auto;flex:1}.cf-text-unit-badge{padding:0 13px;height:42px;display:flex;align-items:center;background:#f1f5f9;border:1.5px solid #e5e8ef;border-left:none;border-radius:0 10px 10px 0;font-size:13px;font-weight:700;color:#475569;white-space:nowrap}@media(max-width:760px){.cf-grid{grid-template-columns:1fr}.cf-field-wrap.cf-full-width{grid-column:1}}';
// styles moved to kegalle-admin.css
}
var isAdmin = !!document.querySelector('.ka-premium-form,.ka-form-grid');
// cf-sd styles now in kegalle-admin.css
var container = document.getElementById('category-fields-container') || document.getElementById('prd-category-fields-container');
var nativeSelect = document.getElementById('cf-category-select') || document.getElementById('prd-cf-category-select');
var titleInput = document.querySelector('[name="title"]');
if (!container || !nativeSelect) return;
var currentFields = [];
var brandGroup = null;
var topCategoryId = null;
var autoTitle = false;
var isClothing = false;
var brandsCache = {};
var modelsCache = {};
var existingValues = window.cfExistingValues || {};
if (!nativeSelect.getAttribute('data-cf-signal')) {
buildSearchableDropdown(nativeSelect);
}
var locationSelect = document.querySelector('select[name="location"]');
if (!locationSelect) locationSelect = document.querySelector('select[name="location_id"]');
if (locationSelect && locationSelect.options.length > 3) {
buildSearchableDropdown(locationSelect);
}
function buildSearchableDropdown(sel) {
var wrap = document.createElement('div');
wrap.className = 'cf-sd-wrap';
var placeholderText = sel.options[0] && !sel.options[0].value ? sel.options[0].textContent : 'Select...';
var display = document.createElement('div');
display.className = 'cf-sd-display placeholder';
display.tabIndex = 0;
display.textContent = placeholderText;
var panel = document.createElement('div');
panel.className = 'cf-sd-panel';
var search = document.createElement('input');
search.className = 'cf-sd-search';
search.type = 'text';
search.placeholder = 'Type to search...';
var list = document.createElement('div');
list.className = 'cf-sd-list';
panel.appendChild(search);
panel.appendChild(list);
wrap.appendChild(display);
wrap.appendChild(panel);
sel.style.display = 'none';
sel.parentNode.insertBefore(wrap, sel.nextSibling);
function hideS2() {
var s2 = sel.parentNode.querySelector('.select2-container');
if (s2) { s2.style.display = 'none'; return true; }
return false;
}
if (!hideS2()) { setTimeout(hideS2, 500); setTimeout(hideS2, 1500); }
var groups = [];
var allItems = [];
var hasOptgroups = false;
var focusedIdx = -1;
var visibleItems = [];
function parseOptions() {
groups = [];
allItems = [];
hasOptgroups = sel.querySelectorAll('optgroup').length > 0;
if (hasOptgroups) {
sel.querySelectorAll('optgroup').forEach(function(og) {
var g = { label: og.label, items: [] };
og.querySelectorAll('option').forEach(function(o) {
var item = { value: o.value, text: o.textContent, groupLabel: og.label };
g.items.push(item);
allItems.push(item);
});
groups.push(g);
});
} else {
var g = { label: null, items: [] };
Array.prototype.forEach.call(sel.options, function(o) {
if (!o.value) return;
var item = { value: o.value, text: o.textContent, groupLabel: '' };
g.items.push(item);
allItems.push(item);
});
groups.push(g);
}
}
function renderList(filter) {
parseOptions();
list.innerHTML = '';
visibleItems = [];
focusedIdx = -1;
var f = (filter || '').toLowerCase();
var hasResults = false;
groups.forEach(function(g) {
var matching = g.items.filter(function(item) {
if (!f) return true;
if (item.text.toLowerCase().indexOf(f) > -1) return true;
if (item.groupLabel && item.groupLabel.toLowerCase().indexOf(f) > -1) return true;
return false;
});
if (!matching.length) return;
hasResults = true;
if (g.label) {
var header = document.createElement('div');
header.className = 'cf-sd-group';
header.textContent = g.label;
list.appendChild(header);
}
matching.forEach(function(item) {
var div = document.createElement('div');
div.className = 'cf-sd-item' + (hasOptgroups ? ' grouped' : '');
if (sel.value === item.value) div.className += ' selected';
div.textContent = item.text;
div.dataset.value = item.value;
div.addEventListener('mousedown', function(e) {
e.preventDefault();
e.stopPropagation();
selectItem(item);
});
list.appendChild(div);
visibleItems.push(div);
});
});
if (!hasResults) {
var empty = document.createElement('div');
empty.className = 'cf-sd-empty';
empty.textContent = 'No results found';
list.appendChild(empty);
}
}
function selectItem(item) {
sel.value = item.value;
if (hasOptgroups && item.groupLabel) {
var parentName = item.groupLabel.replace(/^[^\w]*\s*/, '');
display.textContent = parentName + ' → ' + item.text;
} else {
display.textContent = item.text;
}
display.className = 'cf-sd-display';
closePanel();
sel.dispatchEvent(new Event('change'));
}
function openPanel() {
document.querySelectorAll('.cf-sd-wrap.open').forEach(function(w) {
if (w !== wrap) w.classList.remove('open');
});
wrap.classList.add('open');
search.value = '';
parseOptions();
renderList('');
setTimeout(function() { search.focus(); }, 10);
var selected = list.querySelector('.selected');
if (selected) selected.scrollIntoView({ block: 'center' });
}
function closePanel() {
wrap.classList.remove('open');
focusedIdx = -1;
}
display.addEventListener('mousedown', function(e) {
e.preventDefault();
e.stopPropagation();
if (wrap.classList.contains('open')) closePanel();
else openPanel();
});
display.addEventListener('keydown', function(e) {
if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
e.preventDefault();
openPanel();
}
});
search.addEventListener('input', function() {
renderList(this.value);
});
search.addEventListener('keydown', function(e) {
if (e.key === 'ArrowDown') {
e.preventDefault();
if (visibleItems.length) {
focusedIdx = Math.min(focusedIdx + 1, visibleItems.length - 1);
updateFocus();
}
} else if (e.key === 'ArrowUp') {
e.preventDefault();
if (focusedIdx > 0) { focusedIdx--; updateFocus(); }
} else if (e.key === 'Enter') {
e.preventDefault();
if (focusedIdx >= 0 && visibleItems[focusedIdx]) {
var val = visibleItems[focusedIdx].dataset.value;
var item = allItems.find(function(i) { return i.value === val; });
if (item) selectItem(item);
}
} else if (e.key === 'Escape') {
closePanel();
display.focus();
}
});
function updateFocus() {
visibleItems.forEach(function(el, i) {
el.classList.toggle('focused', i === focusedIdx);
});
if (visibleItems[focusedIdx]) visibleItems[focusedIdx].scrollIntoView({ block: 'nearest' });
}
panel.addEventListener('mousedown', function(e) { e.stopPropagation(); });
document.addEventListener('mousedown', function(e) {
if (!wrap.contains(e.target)) closePanel();
});
function syncDisplay() {
if (sel.value) {
parseOptions();
var selected = allItems.find(function(i) { return i.value === sel.value; });
if (selected) {
if (hasOptgroups && selected.groupLabel) {
display.textContent = selected.groupLabel.replace(/^[^\w]*\s*/, '') + ' → ' + selected.text;
} else {
display.textContent = selected.text;
}
display.className = 'cf-sd-display';
}
} else {
display.textContent = placeholderText;
display.className = 'cf-sd-display placeholder';
}
}
parseOptions();
syncDisplay();
return {
refresh: function() { parseOptions(); syncDisplay(); },
rerender: function() { if (wrap.classList.contains('open')) renderList(search.value); },
reset: function() {
display.textContent = placeholderText;
display.className = 'cf-sd-display placeholder';
}
};
}
nativeSelect.addEventListener('change', function() {
var catId = this.dataset.categoryId || this.value;
container.innerHTML = '';
currentFields = [];
if (!catId) return;
fetch('/api/category-fields/' + catId)
.then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
.then(function(data) {
currentFields = data.fields || [];
brandGroup = data.brand_group;
topCategoryId = data.top_category_id || null;
autoTitle = data.auto_title;
isClothing = /fashion|cloth|men|women|kids|baby|dress|wear/i.test((data.parent_slug || '') + ' ' + (data.category_name || ''));
renderFields();
})
.catch(function() { });
});
function renderFields() {
container.innerHTML = '';
var hasBrand = currentFields.some(function(f) { return f.type === 'brand_select'; });
var hasModel = currentFields.some(function(f) { return f.type === 'model_select'; });
if (!hasBrand) {
var toPrep = [{ name: 'brand_id', label: 'Brand', type: 'brand_select', is_required: false }];
if (!hasModel) toPrep.push({ name: 'model_id', label: 'Model', type: 'model_select', is_required: false });
currentFields = toPrep.concat(currentFields);
}
// Deduplicate fields by name (keep first occurrence)
var seen = {};
currentFields = currentFields.filter(function(f) {
if (seen[f.name]) return false;
seen[f.name] = true;
return true;
});
var hasCondition = currentFields.some(function(f) { return f.name === 'condition'; });
if (!hasCondition) {
currentFields = [
{ name: 'condition', label: 'Condition', type: 'select', is_required: false,
options: ['Brand New', 'Used – Like New', 'Used – Good', 'Used – Fair', 'For Parts / Not Working'] }
].concat(currentFields);
}
// Move checkbox_group (Features) to the end, full-width
currentFields.sort(function(a, b) {
var aLast = a.type === 'checkbox_group' ? 1 : 0;
var bLast = b.type === 'checkbox_group' ? 1 : 0;
return aLast - bLast;
});
var grid = document.createElement('div');
grid.className = 'cf-grid';
var hasCheckboxGroup = currentFields.some(function(f) { return f.type === 'checkbox_group'; });
currentFields.forEach(function(f) {
var wrap = document.createElement('div');
wrap.className = 'cf-field-wrap';
// Force checkbox_group full-width and add divider before it
if (f.type === 'checkbox_group') {
wrap.classList.add('cf-full-width');
var divider = document.createElement('div');
divider.className = 'cf-full-width';
divider.className = 'cf-full-width cf-divider';
grid.appendChild(divider);
var secLabel = document.createElement('div');
secLabel.className = 'cf-full-width';
secLabel.className = 'cf-full-width cf-section-label';
secLabel.textContent = 'Features & Extras';
grid.appendChild(secLabel);
}
var label = document.createElement('label');
label.className = 'cf-label';
label.textContent = f.label;
if (f.is_required) {
var req = document.createElement('span');
req.className = 'cf-req';
req.textContent = ' *';
label.appendChild(req);
}
wrap.appendChild(label);
var existVal = existingValues['cf_' + f.name] || '';
if (f.type === 'brand_select') {
var sel = createSelect(f, []);
sel.id = 'cf-brand-select';
wrap.appendChild(sel);
var sd = buildSearchableDropdown(sel);
loadBrands(sel, brandGroup || 'all', existVal, sd, topCategoryId);
} else if (f.type === 'model_select') {
var sel = createSelect(f, []);
sel.id = 'cf-model-select';
wrap.appendChild(sel);
var sd = buildSearchableDropdown(sel);
sel._sd = sd;
if (existVal) sel.dataset.pendingValue = existVal;
} else if (f.type === 'select') {
var sel = createSelect(f, f.options || []);
if (existVal) sel.value = existVal;
wrap.appendChild(sel);
buildSearchableDropdown(sel);
} else if (f.type === 'checkbox_group') {
var cbWrap = document.createElement('div');
cbWrap.className = 'cf-checkbox-group';
var checked = existVal ? existVal.split(',') : [];
(f.options || []).forEach(function(opt) {
var cbLabel = document.createElement('label');
cbLabel.className = 'cf-checkbox-label' + (checked.indexOf(opt) > -1 ? ' cf-checked' : '');
var cb = document.createElement('input');
cb.type = 'checkbox';
cb.name = 'cf_' + f.name + '[]';
cb.value = opt;
if (checked.indexOf(opt) > -1) cb.checked = true;
cbLabel.addEventListener('click', function(e) {
e.preventDefault();
cb.checked = !cb.checked;
cbLabel.classList.toggle('cf-checked', cb.checked);
maybeUpdateTitle();
});
cbLabel.appendChild(cb);
cbLabel.appendChild(document.createTextNode(' ' + opt));
cbWrap.appendChild(cbLabel);
});
wrap.appendChild(cbWrap);
} else if (f.type === 'pill_group') {
var isMulti = !!f.multi;
var pillWrap = document.createElement('div');
pillWrap.className = 'cf-pill-group';
var checked = existVal ? existVal.split(',') : [];
(f.options || []).forEach(function(opt) {
var pill = document.createElement('button');
pill.type = 'button';
pill.className = 'cf-pill' + (checked.indexOf(opt) > -1 ? ' selected' : '');
pill.textContent = opt;
pill.dataset.value = opt;
pill.addEventListener('click', function() {
if (isMulti) { this.classList.toggle('selected'); }
else { pillWrap.querySelectorAll('.cf-pill').forEach(function(p){ p.classList.remove('selected'); }); this.classList.add('selected'); }
syncPH(); maybeUpdateTitle();
});
pillWrap.appendChild(pill);
});
var pillHidden = document.createElement('input');
pillHidden.type = 'hidden';
pillHidden.name = 'cf_' + f.name;
if (existVal) pillHidden.value = existVal;
function syncPH() { pillHidden.value = Array.from(pillWrap.querySelectorAll('.cf-pill.selected')).map(function(p){ return p.dataset.value; }).join(','); }
wrap.appendChild(pillWrap);
wrap.appendChild(pillHidden);
if (f.full_width) wrap.classList.add('cf-full-width');
} else if (f.type === 'text_unit') {
var unitWrap = document.createElement('div');
unitWrap.className = 'cf-text-unit-wrap';
var inp = document.createElement('input');
inp.type = 'number';
inp.name = 'cf_' + f.name;
inp.className = 'cf-input';
inp.placeholder = f.placeholder || '0';
inp.min = '0';
inp.setAttribute('inputmode', 'decimal');
if (existVal) inp.value = existVal;
inp.addEventListener('input', maybeUpdateTitle);
var badge = document.createElement('div');
badge.className = 'cf-text-unit-badge';
badge.textContent = f.unit || '';
unitWrap.appendChild(inp); unitWrap.appendChild(badge);
wrap.appendChild(unitWrap);
} else if (f.type === 'number') {
var inp = document.createElement('input');
inp.type = 'number';
inp.name = 'cf_' + f.name;
inp.className = 'cf-input';
inp.placeholder = f.placeholder || '';
if (existVal) inp.value = existVal;
inp.addEventListener('input', maybeUpdateTitle);
wrap.appendChild(inp);
} else {
var inp = document.createElement('input');
inp.type = 'text';
inp.name = 'cf_' + f.name;
inp.className = 'cf-input';
inp.placeholder = f.placeholder || '';
if (existVal) inp.value = existVal;
inp.addEventListener('input', maybeUpdateTitle);
wrap.appendChild(inp);
}
grid.appendChild(wrap);
});
container.appendChild(grid);
if (isClothing) {
renderSizeVariants(container);
}
if (autoTitle && titleInput) {
titleInput.dataset.autoTitle = '1';
maybeUpdateTitle();
} else if (titleInput) {
delete titleInput.dataset.autoTitle;
}
}
function renderSizeVariants(container) {
var existing = window.listingVariants || {};
var sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
var wrap = document.createElement('div');
wrap.className = 'cf-field-wrap';
wrap.className = 'cf-field-wrap cf-size-wrap';
var label = document.createElement('div');
label.className = 'cf-size-title';
label.textContent = 'Sizes & Prices';
wrap.appendChild(label);
var hint = document.createElement('div');
hint.className = 'cf-size-hint';
hint.textContent = 'Tick the sizes you have. Leave a price empty to use the main listing price for that size.';
wrap.appendChild(hint);
var rowsBox = document.createElement('div');
rowsBox.className = 'cf-size-rows';
sizes.forEach(function(size) {
var has = Object.prototype.hasOwnProperty.call(existing, size);
var row = document.createElement('div');
row.className = 'cf-size-row' + (has ? ' cf-size-checked' : '');
var cb = document.createElement('input');
cb.type = 'checkbox';
cb.name = 'variant_sizes[]';
cb.value = size;
cb.id = 'vsize-' + size;
if (has) cb.checked = true;
var cbLabel = document.createElement('label');
cbLabel.setAttribute('for', 'vsize-' + size);
cbLabel.className = 'cf-size-check-label';
cbLabel.textContent = size;
var price = document.createElement('input');
price.type = 'number';
price.name = 'variant_price[' + size + ']';
price.placeholder = 'Price (LKR)';
price.min = '0';
price.step = '0.01';
price.setAttribute('inputmode', 'decimal');
price.className = 'cf-size-price' + (has ? '' : ' hidden');
if (has && existing[size]) price.value = existing[size];
cb.addEventListener('change', function() {
price.classList.toggle('hidden', !cb.checked);
row.classList.toggle('cf-size-checked', cb.checked);
});
row.appendChild(cb);
row.appendChild(cbLabel);
row.appendChild(price);
rowsBox.appendChild(row);
});
wrap.appendChild(rowsBox);
container.appendChild(wrap);
}
function createSelect(f, opts) {
var sel = document.createElement('select');
sel.name = 'cf_' + f.name;
sel.className = 'cf-select';
var def = document.createElement('option');
def.value = '';
def.textContent = 'Select ' + f.label + '...';
sel.appendChild(def);
opts.forEach(function(o) {
var opt = document.createElement('option');
opt.value = o;
opt.textContent = o;
sel.appendChild(opt);
});
sel.addEventListener('change', maybeUpdateTitle);
return sel;
}
function loadBrands(sel, group, existVal, sd, catId) {
if (catId) {
var cacheKey = 'cat_' + catId;
if (brandsCache[cacheKey]) { populateBrandSelect(sel, brandsCache[cacheKey], existVal, sd); return; }
fetch('/api/brands/by-category/' + catId)
.then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
.then(function(brands) {
brandsCache[cacheKey] = brands;
populateBrandSelect(sel, brands, existVal, sd);
})
.catch(function() {});
return;
}
if (brandsCache[group]) { populateBrandSelect(sel, brandsCache[group], existVal, sd); return; }
fetch('/api/brands/' + group)
.then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
.then(function(brands) {
brandsCache[group] = brands;
populateBrandSelect(sel, brands, existVal, sd);
})
.catch(function() {});
}
function populateBrandSelect(sel, brands, existVal, sd) {
sel.innerHTML = '';
var def = document.createElement('option');
def.value = '';
def.textContent = 'Select Brand...';
sel.appendChild(def);
brands.forEach(function(b) {
var o = document.createElement('option');
o.value = b.id;
o.textContent = b.name;
sel.appendChild(o);
});
if (existVal) sel.value = existVal;
if (sd) sd.refresh();
sel.addEventListener('change', function() {
var brandId = this.value;
var modelSel = document.getElementById('cf-model-select');
if (!modelSel) return;
modelSel.innerHTML = '<option value="">Select Model...</option>';
if (modelSel._sd) modelSel._sd.reset();
if (!brandId) return;
loadModels(modelSel, brandId);
});
var triggerVal = existVal || sel.value;
if (triggerVal) sel.dispatchEvent(new Event('change'));
}
function loadModels(sel, brandId) {
if (modelsCache[brandId]) { populateModelSelect(sel, modelsCache[brandId]); return; }
fetch('/api/brand-models/' + brandId)
.then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
.then(function(models) {
modelsCache[brandId] = models;
populateModelSelect(sel, models);
})
.catch(function() {});
}
function populateModelSelect(sel, models) {
sel.innerHTML = '';
var def = document.createElement('option');
def.value = '';
def.textContent = 'Select Model...';
sel.appendChild(def);
models.forEach(function(m) {
var o = document.createElement('option');
o.value = m.id;
o.textContent = m.name;
sel.appendChild(o);
});
var pending = sel.dataset.pendingValue;
if (pending) { sel.value = pending; delete sel.dataset.pendingValue; }
if (sel._sd) { sel._sd.refresh(); sel._sd.rerender(); }
sel.addEventListener('change', maybeUpdateTitle);
}
function maybeUpdateTitle() {
if (!titleInput || !titleInput.dataset.autoTitle) return;
if (titleInput.dataset.userEdited === '1') return;
var parts = [];
var brandSel = document.getElementById('cf-brand-select');
var modelSel = document.getElementById('cf-model-select');
if (brandSel && brandSel.selectedIndex > 0) parts.push(brandSel.options[brandSel.selectedIndex].text);
if (modelSel && modelSel.selectedIndex > 0) parts.push(modelSel.options[modelSel.selectedIndex].text);
var yearSel = container.querySelector('[name="cf_year"]');
var ramSel = container.querySelector('[name="cf_ram"]');
var memSel = container.querySelector('[name="cf_memory"]');
var transSel = container.querySelector('[name="cf_transmission"]');
if (yearSel && yearSel.value) parts.push(yearSel.value);
if (ramSel && ramSel.value) parts.push(ramSel.value + ' RAM');
if (memSel && memSel.value) parts.push(memSel.value);
if (transSel && transSel.value) parts.push(transSel.value);
if (parts.length) titleInput.value = parts.join(' — ');
}
if (titleInput) {
titleInput.addEventListener('keydown', function() { this.dataset.userEdited = '1'; });
}
if (nativeSelect.dataset.categoryId || nativeSelect.value) {
nativeSelect.dispatchEvent(new Event('change'));
}
})();