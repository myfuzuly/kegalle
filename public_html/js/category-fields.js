/**
 * Dynamic category-specific fields for listing creation/editing.
 * All selects become searchable dropdowns.
 */
(function(){
    'use strict';

    // Inject CSS if not already loaded (admin pages)
    if (!document.querySelector('link[href*="kurulla-main"]')) {
        var s = document.createElement('style');
        s.textContent = '.cf-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-top:18px;padding:20px;background:#f7f8fa;border-radius:12px;border:1px solid #e5e8ef}.cf-field-wrap{display:flex;flex-direction:column;gap:6px}.cf-label{font-size:13px;font-weight:600;color:#0d1b2a}.cf-req{color:#d32f2f}.cf-input,.cf-select{width:100%;padding:10px 12px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:14px;color:#0d1b2a;background:#fff;transition:border-color .2s;box-sizing:border-box}.cf-input:focus,.cf-select:focus{outline:none;border-color:#43a047;box-shadow:0 0 0 3px rgba(27,94,32,.08)}.cf-checkbox-group{display:flex;flex-wrap:wrap;gap:8px}.cf-checkbox-label{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:10px;border:1.5px solid #e5e8ef;background:#fff;font-size:13px;color:#667085;cursor:pointer;transition:all .2s;user-select:none}.cf-checkbox-label:hover{border-color:#43a047;color:#1b5e20}.cf-checkbox-label input[type=checkbox]{width:16px;height:16px;accent-color:#1b5e20}@media(max-width:760px){.cf-grid{grid-template-columns:1fr}}';
        document.head.appendChild(s);
    }

    var isAdmin = !!document.querySelector('.ka-premium-form,.ka-form-grid');
    var sdCss = document.createElement('style');
    sdCss.textContent = [
        '.cf-sd-wrap{position:relative;min-width:0;width:100%}',
        '.cf-sd-display{width:100%;padding:10px 36px 10px 12px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:14px;font-weight:400;color:#0d1b2a;background:#fff url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'10\' height=\'6\'%3E%3Cpath d=\'M1 1l4 4 4-4\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' fill=\'none\' stroke-linecap=\'round\'/%3E%3C/svg%3E") right 12px center no-repeat;cursor:pointer;transition:border-color .16s,box-shadow .16s;box-sizing:border-box;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-family:inherit;line-height:1.4}',
        // Dashboard form overrides
        '.kd-form .cf-sd-display{padding:15px 40px 15px 15px;border:1px solid #dbe3ee;border-radius:16px}',
        '.kd-form .cf-sd-panel{border-radius:16px}',
        '.kd-form .cf-sd-search{border-radius:16px 16px 0 0}',
        // Admin form overrides
        '.ka-field .cf-sd-wrap{width:100%!important;max-width:100%!important}',
        '.ka-field .cf-sd-display{min-height:44px;padding:0 36px 0 14px;border-radius:13px;font-weight:600;display:flex;align-items:center;border:1px solid #e7ecf3;box-shadow:0 8px 22px rgba(15,23,42,.035);width:100%!important;max-width:100%!important}',
        // Focus states
        '.cf-sd-display:focus,.cf-sd-wrap.open .cf-sd-display{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.12);outline:none}',
        '.ka-field .cf-sd-wrap.open .cf-sd-display,.ka-field .cf-sd-display:focus{border-color:rgba(29,158,117,.55)!important;box-shadow:0 0 0 3px rgba(29,158,117,.10)!important}',
        '.cf-sd-display.placeholder{color:#94a3b8}',
        // Panel
        '.cf-sd-panel{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:220px;width:100%;background:#fff;border:1.5px solid #e5e8ef;border-radius:10px;box-shadow:0 12px 36px rgba(15,23,42,.12);z-index:200;max-height:280px;overflow:hidden;flex-direction:column}',
        '.ka-field .cf-sd-panel{border-radius:13px;border-color:#e7ecf3;min-width:260px}',
        '.cf-sd-wrap.open .cf-sd-panel{display:flex}',
        // Search input inside panel
        '.cf-sd-search{width:100%;padding:10px 12px;border:none!important;border-bottom:1px solid #e8eef5!important;font-size:13px;font-family:inherit;outline:none!important;box-sizing:border-box;border-radius:10px 10px 0 0!important;background:#f8fafc!important;min-height:auto!important;box-shadow:none!important;font-weight:400!important;color:#0d1b2a!important}',
        '.cf-sd-search::placeholder{color:#94a3b8!important}',
        // List
        '.cf-sd-list{overflow-y:auto;flex:1;padding:4px 0}',
        '.cf-sd-group{padding:8px 12px 4px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.5px;position:sticky;top:0;background:#fff}',
        '.cf-sd-item{padding:8px 12px 8px 12px;font-size:13px;color:#0f172a;cursor:pointer;transition:background .15s}',
        '.cf-sd-item.grouped{padding-left:24px}',
        '.cf-sd-item:hover,.cf-sd-item.focused{background:#ecfdf5}',
        '.cf-sd-item.selected{background:#dcfce7;font-weight:600;color:#047857}',
        '.cf-sd-empty{padding:16px;text-align:center;color:#94a3b8;font-size:13px}',
    ].join('');
    document.head.appendChild(sdCss);

    var container = document.getElementById('category-fields-container');
    var nativeSelect = document.getElementById('cf-category-select');
    var titleInput = document.querySelector('[name="title"]');
    if (!container || !nativeSelect) return;

    var currentFields = [];
    var brandGroup = null;
    var autoTitle = false;
    var isClothing = false;
    var brandsCache = {};
    var modelsCache = {};
    var existingValues = window.cfExistingValues || {};

    // ── Build searchable dropdowns for category & location ─────
    buildSearchableDropdown(nativeSelect);

    var locationSelect = document.querySelector('select[name="location"]');
    if (!locationSelect) locationSelect = document.querySelector('select[name="location_id"]');
    if (locationSelect && locationSelect.options.length > 3) {
        buildSearchableDropdown(locationSelect);
    }

    /**
     * Converts a native <select> into a searchable dropdown.
     * Returns { refresh() } to rebuild options after async population.
     */
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

        // Hide Select2 if present
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
            // Close all other open panels first
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

        // Pre-select if value set
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
            reset: function() {
                display.textContent = placeholderText;
                display.className = 'cf-sd-display placeholder';
            }
        };
    }

    // ── Category change → load fields ──────────────────────────
    nativeSelect.addEventListener('change', function() {
        var catId = this.value;
        container.innerHTML = '';
        currentFields = [];
        if (!catId) return;
        fetch('/api/category-fields/' + catId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                currentFields = data.fields || [];
                brandGroup = data.brand_group;
                autoTitle = data.auto_title;
                isClothing = /fashion|cloth|men|women|kids|baby|dress|wear/i.test((data.parent_slug || '') + ' ' + (data.category_name || ''));
                renderFields();
            });
    });

    // ── Render fields ──────────────────────────────────────────
    function renderFields() {
        container.innerHTML = '';

        // Brand & Model available for every category
        var hasBrand = currentFields.some(function(f) { return f.type === 'brand_select'; });
        if (!hasBrand) {
            currentFields = [
                { name: 'brand_id', label: 'Brand', type: 'brand_select', is_required: false },
                { name: 'model_id', label: 'Model', type: 'model_select', is_required: false }
            ].concat(currentFields);
        }

        // Condition available for every category
        var hasCondition = currentFields.some(function(f) { return f.name === 'condition'; });
        if (!hasCondition) {
            currentFields = [
                { name: 'condition', label: 'Condition', type: 'select', is_required: false,
                  options: ['Brand New', 'Used – Like New', 'Used – Good', 'Used – Fair', 'For Parts / Not Working'] }
            ].concat(currentFields);
        }

        var grid = document.createElement('div');
        grid.className = 'cf-grid';

        currentFields.forEach(function(f) {
            var wrap = document.createElement('div');
            wrap.className = 'cf-field-wrap';

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
                loadBrands(sel, brandGroup || 'all', existVal, sd);
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
                    cbLabel.className = 'cf-checkbox-label';
                    var cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.name = 'cf_' + f.name + '[]';
                    cb.value = opt;
                    if (checked.indexOf(opt) > -1) cb.checked = true;
                    cb.addEventListener('change', maybeUpdateTitle);
                    cbLabel.appendChild(cb);
                    cbLabel.appendChild(document.createTextNode(' ' + opt));
                    cbWrap.appendChild(cbLabel);
                });
                wrap.appendChild(cbWrap);
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

    // ── Size & price variants (clothing) ────────────────────────
    function renderSizeVariants(container) {
        var existing = window.listingVariants || {};
        var sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        var wrap = document.createElement('div');
        wrap.className = 'cf-field-wrap';
        wrap.style.cssText = 'margin-top:14px;background:#f8fafc;border:1.5px solid #e5e8ef;border-radius:12px;padding:14px 16px';

        var label = document.createElement('div');
        label.style.cssText = 'font-weight:700;font-size:13.5px;margin-bottom:4px';
        label.textContent = 'Sizes & Prices';
        wrap.appendChild(label);

        var hint = document.createElement('div');
        hint.style.cssText = 'font-size:12px;color:#667085;margin-bottom:12px';
        hint.textContent = 'Tick the sizes you have. Leave a price empty to use the main listing price for that size.';
        wrap.appendChild(hint);

        var rowsBox = document.createElement('div');
        rowsBox.style.cssText = 'display:flex;flex-wrap:wrap;gap:10px';

        sizes.forEach(function(size) {
            var has = Object.prototype.hasOwnProperty.call(existing, size);
            var row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid ' + (has ? '#1B5E20' : '#e5e8ef') + ';border-radius:10px;padding:8px 12px';

            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = 'variant_sizes[]';
            cb.value = size;
            cb.id = 'vsize-' + size;
            if (has) cb.checked = true;

            var cbLabel = document.createElement('label');
            cbLabel.setAttribute('for', 'vsize-' + size);
            cbLabel.style.cssText = 'font-weight:700;font-size:13px;min-width:30px;cursor:pointer';
            cbLabel.textContent = size;

            var price = document.createElement('input');
            price.type = 'number';
            price.name = 'variant_price[' + size + ']';
            price.placeholder = 'Price (LKR)';
            price.min = '0';
            price.step = '0.01';
            price.setAttribute('inputmode', 'decimal');
            price.style.cssText = 'width:110px;height:34px;border:1px solid #e5e8ef;border-radius:8px;padding:0 10px;font-size:12.5px;' + (has ? '' : 'display:none');
            if (has && existing[size]) price.value = existing[size];

            cb.addEventListener('change', function() {
                price.style.display = cb.checked ? '' : 'none';
                row.style.borderColor = cb.checked ? '#1B5E20' : '#e5e8ef';
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

    // ── Brand/Model cascading ──────────────────────────────────
    function loadBrands(sel, group, existVal, sd) {
        if (brandsCache[group]) { populateBrandSelect(sel, brandsCache[group], existVal, sd); return; }
        fetch('/api/brands/' + group)
            .then(function(r) { return r.json(); })
            .then(function(brands) {
                brandsCache[group] = brands;
                populateBrandSelect(sel, brands, existVal, sd);
            });
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
        if (existVal) sel.dispatchEvent(new Event('change'));
    }

    function loadModels(sel, brandId) {
        if (modelsCache[brandId]) { populateModelSelect(sel, modelsCache[brandId]); return; }
        fetch('/api/brand-models/' + brandId)
            .then(function(r) { return r.json(); })
            .then(function(models) {
                modelsCache[brandId] = models;
                populateModelSelect(sel, models);
            });
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
        if (sel._sd) sel._sd.refresh();
        sel.addEventListener('change', maybeUpdateTitle);
    }

    // ── Auto-title generation ──────────────────────────────────
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

    // ── Auto-trigger on page load (for editing) ────────────────
    if (nativeSelect.value) {
        nativeSelect.dispatchEvent(new Event('change'));
    }
})();
