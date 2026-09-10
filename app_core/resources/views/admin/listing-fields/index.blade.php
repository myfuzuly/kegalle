@extends('layouts.admin')
@section('title','Listing Fields')
@section('page','Listing Fields')
@section('heading','Listing Fields')
@section('subheading','Custom spec fields, category assignments and brand/model data')



@php
$typeOptions = [
    'select'         => ['label'=>'Select (Dropdown)', 'icon'=>'▼',  'cls'=>'lft-type-select'],
    'text'           => ['label'=>'Text',              'icon'=>'T',   'cls'=>'lft-type-text'],
    'number'         => ['label'=>'Number',            'icon'=>'#',   'cls'=>'lft-type-number'],
    'checkbox_group' => ['label'=>'Checkbox Group',    'icon'=>'☑',  'cls'=>'lft-type-checkbox_group'],
    'pill_group'     => ['label'=>'Pill Selector',     'icon'=>'💊', 'cls'=>'lft-type-pill_group'],
    'text_unit'      => ['label'=>'Number + Unit',     'icon'=>'📐', 'cls'=>'lft-type-text_unit'],
    'brand_select'   => ['label'=>'Brand Select',      'icon'=>'🏷️','cls'=>'lft-type-brand_select'],
    'model_select'   => ['label'=>'Model Select',      'icon'=>'🔩', 'cls'=>'lft-type-model_select'],
];
@endphp

@section('content')
<div class="lf-wrap">

@if(session('success'))
<div class="lf-flash">✓ {{ session('success') }}</div>
@endif

{{-- Stat cards --}}
<div class="lf-stats">
    <div class="lf-stat">
        <div class="lf-stat-icon green">⚙️</div>
        <div>
            <div class="lf-stat-val">{{ $fields->count() }}</div>
            <div class="lf-stat-lbl">Total Fields</div>
        </div>
    </div>
    <div class="lf-stat">
        <div class="lf-stat-icon blue">📋</div>
        <div>
            <div class="lf-stat-val">{{ $categories->filter(fn($c)=>$c->customFields->count())->count() }}</div>
            <div class="lf-stat-lbl">Categories Assigned</div>
        </div>
    </div>
    <div class="lf-stat">
        <div class="lf-stat-icon amber">⚠️</div>
        <div>
            <div class="lf-stat-val">{{ $fields->where('is_required',true)->count() }}</div>
            <div class="lf-stat-lbl">Required Fields</div>
        </div>
    </div>
    <div class="lf-stat">
        <div class="lf-stat-icon purple">🔍</div>
        <div>
            <div class="lf-stat-val">{{ $fields->where('is_searchable',true)->count() }}</div>
            <div class="lf-stat-lbl">Searchable Fields</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="lf-tabs">
    <a href="?tab=fields" class="lf-tab {{ $tab==='fields' ? 'active':'' }}">⚙️ Custom Fields</a>
    <a href="?tab=assign" class="lf-tab {{ $tab==='assign' ? 'active':'' }}">📋 Category Assignments</a>
    <a href="/admin/brands" class="lf-tab lf-tab-ext">🏷️ Brands &amp; Models →</a>
</div>

{{-- ══ TAB: Custom Fields ══ --}}
@if($tab === 'fields')

{{-- Add field card --}}
<div class="lf-card" id="lftAddCard">
    <div class="lf-card-head lf-card-head-btn" id="lftAddCardHead">
        <div class="lf-card-icon green">✚</div>
        <span class="lf-card-title">Add New Custom Field</span>
        <span class="lf-add-meta">
            <span id="lftAddToggleLabel" class="lf-toggle-lbl">Expand</span>
            <svg id="lftAddChevron" class="lf-chevron-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
        </span>
    </div>
    <div id="lftAddFormBody" class="k-hidden">
    <form method="post" action="/admin/listing-fields/fields">
    @csrf
    <input type="hidden" name="type" id="lftAddTypeVal" value="select">
    <div class="lf-form-body">
        <div class="lf-grid lf-grid--mb">
            <div class="lf-field">
                <label class="lf-label">Label <span class="lf-req">*</span></label>
                <input name="label" required class="lf-input" placeholder="e.g. Condition, Year, Bedrooms">
            </div>
            <div class="lf-field">
                <label class="lf-label">Field Type <span class="lf-req">*</span></label>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="lftAddTypeTrigger">
                        <span class="ksd-trigger-text" id="lftAddTypeLabel">
                            <span class="lf-type lft-type-select">▼ Select (Dropdown)</span>
                        </span>
                        <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="lftAddTypeDropdown">
                        <div class="ksd-list">
                            @foreach($typeOptions as $val => $opt)
                            <div class="ksd-item {{ $val==='select' ? 'ksd-selected':'' }}" data-value="{{ $val }}">
                                <span class="lf-type lft-type-{{ $val }}">{{ $opt['icon'] }} {{ $opt['label'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lf-grid lf-grid--mb">
            <div class="lf-field lf-full" id="lftAddOptionsWrap">
                <label class="lf-label">Options <span class="lf-hint">(comma-separated — for Select, Checkbox Group &amp; Pill Selector)</span></label>
                <input name="options" class="lf-input" placeholder="New, Used, Reconditioned, Antique">
            </div>
            <div class="lf-field k-hidden" id="lftAddUnitWrap">
                <label class="lf-label">Unit <span class="lf-hint">(e.g. MP, inch, mAh, GB)</span></label>
                <input name="unit" class="lf-input" placeholder="MP">
            </div>
            <div class="lf-field">
                <label class="lf-label">Placeholder text</label>
                <input name="placeholder" class="lf-input" placeholder="Select condition…">
            </div>
            <div class="lf-field">
                <label class="lf-label">Sort Order</label>
                <input name="sort_order" type="number" class="lf-input" value="0">
            </div>
        </div>
        <div class="lf-checks">
            <label class="lf-check"><input type="checkbox" name="is_required" value="1"> Required</label>
            <label class="lf-check"><input type="checkbox" name="is_searchable" value="1" checked> Searchable</label>
            <label class="lf-check k-hidden" id="lftAddMultiWrap"><input type="checkbox" name="multi" value="1"> Allow multiple selections (Pill)</label>
            <label class="lf-check k-hidden" id="lftAddFullWrap"><input type="checkbox" name="full_width" value="1"> Full-width row</label>
        </div>
    </div>
    <div class="lf-form-foot">
        <button type="submit" class="lf-btn-primary">+ Add Field</button>
        <button type="button" class="lf-btn-light" id="lftCancelAddBtn">Cancel</button>
    </div>
    </form>
    </div>
</div>

{{-- Fields table --}}
<div class="lf-card">
    <div class="lf-search-bar">
        <div class="lf-search-rel">
            <svg class="lf-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="lftSearch" placeholder="Search fields by label or key…" autocomplete="off" class="lf-search-input">
            <button type="button" id="lftSearchClear" class="lf-search-clear k-hidden">✕</button>
        </div>
        <div class="lf-filter-row" id="lftTypeFilters">
            <button type="button" class="lf-fpill active" data-type="">All</button>
            @foreach($typeOptions as $val => $opt)
            <button type="button" class="lf-fpill" data-type="{{ $val }}">{{ $opt['icon'] }} {{ $opt['label'] }}</button>
            @endforeach
            <span id="lftCountBadge" class="lf-count-badge">{{ $fields->count() }} fields</span>
        </div>
    </div>

    <div class="lf-table-wrap">
    <table class="lf-table" id="lftFieldsTable">
    <thead><tr>
        <th>Label</th><th>Field Key</th><th>Type</th><th>Options</th><th class="lf-th-c">Categories</th><th class="lf-th-c">Sort</th><th>Actions</th>
    </tr></thead>
    <tbody id="lftFieldsTbody">
    @forelse($fields as $field)
    @php $fOpt = $typeOptions[$field->type] ?? ['label'=>$field->type,'cls'=>'lft-type-text']; @endphp
    <tr class="lft-data-row" id="lft-row-{{ $field->id }}"
        data-label="{{ strtolower($field->label) }}"
        data-key="{{ strtolower($field->name) }}"
        data-type="{{ $field->type }}">
        <td>
            <div class="lf-label-cell">
                <span class="lf-label-name">{{ $field->label }}</span>
                <div class="lf-badge-row">
                @if($field->is_required)<span class="lf-badge-req">Required</span>@endif
                @if($field->is_searchable)<span class="lf-badge-srch">Searchable</span>@endif
                </div>
            </div>
        </td>
        <td><code class="lf-code">{{ $field->name }}</code></td>
        <td><span class="lf-type {{ $fOpt['cls'] }}">{{ $fOpt['label'] }}</span></td>
        <td class="lf-td-opts">
            @if($field->options)
            <div class="lf-opts-wrap">
                @foreach(array_slice($field->options,0,3) as $opt)<span class="lf-opt-pill">{{ $opt }}</span>@endforeach
                @if(count($field->options)>3)<span class="lf-opt-more">+{{ count($field->options)-3 }}</span>@endif
            </div>
            @else<span class="lf-opt-dash">—</span>@endif
        </td>
        <td class="lf-th-c">
            @if($field->categories_count > 0)
            <a href="?tab=assign" class="lf-cat-link-count">{{ $field->categories_count }}</a>
            @else
            <a href="?tab=assign" class="lf-cat-link-assign">+ Assign</a>
            @endif
        </td>
        <td class="lf-th-c"><span class="lf-sort-badge">{{ $field->sort_order }}</span></td>
        <td>
            <div class="lf-actions">
                <button type="button" class="lf-act lft-edit-btn" data-fid="{{ $field->id }}">
                    <svg class="lf-svg-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit
                </button>
                <form method="post" action="/admin/listing-fields/fields/{{ $field->id }}" class="lft-del-form" data-label="{{ addslashes($field->label) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="lf-act danger">
                        <svg class="lf-svg-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>Delete
                    </button>
                </form>
            </div>
        </td>
    </tr>
    {{-- Inline edit row --}}
    <tr id="lft-edit-{{ $field->id }}" class="k-hidden">
        <td colspan="7" class="lf-inline-edit">
            <form method="post" action="/admin/listing-fields/fields/{{ $field->id }}">
            @csrf @method('PUT')
            <input type="hidden" name="type" id="lftType-{{ $field->id }}" value="{{ $field->type }}">
            <div class="lf-grid lf-grid--mb">
                <div class="lf-field">
                    <label class="lf-label">Label</label>
                    <input name="label" class="lf-input" value="{{ $field->label }}" required>
                </div>
                <div class="lf-field">
                    <label class="lf-label">Type</label>
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger ksd-has-value" id="lftTypeTrigger-{{ $field->id }}">
                            <span class="ksd-trigger-text" id="lftTypeLabel-{{ $field->id }}">
                                <span class="lf-type {{ $typeOptions[$field->type]['cls'] ?? 'lft-type-text' }}">{{ $typeOptions[$field->type]['label'] ?? $field->type }}</span>
                            </span>
                            <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="lftTypeDropdown-{{ $field->id }}">
                            <div class="ksd-list">
                                @foreach($typeOptions as $val => $opt)
                                <div class="ksd-item {{ $field->type===$val ? 'ksd-selected':'' }}" data-value="{{ $val }}" data-fid="{{ $field->id }}">
                                    <span class="lf-type lft-type-{{ $val }}">{{ $opt['icon'] }} {{ $opt['label'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lf-field lf-full">
                    <label class="lf-label">Options <span class="lf-hint">(comma-separated)</span></label>
                    <input name="options" class="lf-input" value="{{ $field->options ? implode(', ', $field->options) : '' }}">
                </div>
                <div class="lf-field">
                    <label class="lf-label">Placeholder</label>
                    <input name="placeholder" class="lf-input" value="{{ $field->placeholder }}">
                </div>
                <div class="lf-field">
                    <label class="lf-label">Sort Order</label>
                    <input name="sort_order" type="number" class="lf-input" value="{{ $field->sort_order }}">
                </div>
                <div class="lf-field lf-full">
                    <div class="lf-checks">
                        <label class="lf-check"><input type="checkbox" name="is_required" value="1" @checked($field->is_required)> Required</label>
                        <label class="lf-check"><input type="checkbox" name="is_searchable" value="1" @checked($field->is_searchable)> Searchable</label>
                    </div>
                </div>
            </div>
            <div class="lf-edit-foot">
                <button type="submit" class="lf-btn-primary lf-btn-sm">
                    <svg class="lf-svg-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>Save Changes
                </button>
                <button type="button" class="lf-btn-light lf-btn-sm lft-cancel-edit-btn" data-fid="{{ $field->id }}">Cancel</button>
            </div>
            </form>
        </td>
    </tr>
    @empty
    <tr id="lft-empty-state">
        <td colspan="7" class="lf-empty-cell">
            <div class="lf-empty-icon">⚙️</div>
            <div class="lf-empty-title">No custom fields yet</div>
            <div class="lf-empty-msg">Click "Add New Custom Field" above to create your first field.</div>
            <button type="button" id="lftAddFirstBtn" class="lf-btn-primary lf-btn-sm">+ Add First Field</button>
        </td>
    </tr>
    @endforelse
    </tbody>
    </table>
    </div>

    <div id="lftNoResults" class="lf-no-results k-hidden">
        <div class="lf-no-results-icon">🔍</div>
        <div class="lf-no-results-title">No fields match</div>
        <div class="lf-no-results-msg">Try a different search or clear the filter.</div>
        <button type="button" id="lftClearSearchBtn" class="lf-btn-light lf-btn-sm">Clear Search</button>
    </div>

    <div id="lftPagination" class="lf-pagination k-hidden">
        <span id="lftPageInfo" class="lf-page-info"></span>
        <div class="lf-page-nums">
            <button type="button" id="lftPrevBtn" class="lf-page-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>Prev
            </button>
            <div id="lftPageNumbers" class="lf-page-nums"></div>
            <button type="button" id="lftNextBtn" class="lf-page-btn">
                Next<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        </div>
        <select id="lftPerPage" class="lf-per-page">
            <option value="10">10 / page</option>
            <option value="20">20 / page</option>
            <option value="50">50 / page</option>
            <option value="100">All</option>
        </select>
    </div>
</div>
@endif

{{-- ══ TAB: Category Assignments ══ --}}
@if($tab === 'assign')

<div class="lf-info-banner">
    <span class="lf-info-icon">💡</span>
    <div>
        <div class="lf-info-title">How Category Assignments work</div>
        <div class="lf-info-body"><strong>Step 1:</strong> Select a subcategory below. &nbsp;<strong>Step 2:</strong> Tick the fields to show when someone creates a listing in that category. &nbsp;<strong>Step 3:</strong> Click Save.</div>
    </div>
</div>

<div class="lf-card">
    <div class="lf-card-head">
        <div class="lf-card-icon purple">📋</div>
        <span class="lf-card-title" id="lftCardTitle">Assign Fields to Category</span>
        <span class="lf-card-meta k-hidden lf-assigned-count" id="lftAssignedCount"></span>
    </div>
    <form method="post" action="/admin/listing-fields/assign">
    @csrf
    <input type="hidden" name="category_id" id="lftCatNative" value="" required>

    <div class="lf-step-area">
        <div class="lf-step-row">
            <span class="lf-step-num">1</span>
            <span class="lf-step-lbl">Select a subcategory</span>
        </div>
        <div class="ksd-wrap lf-cat-ksd-wrap">
            <button type="button" class="ksd-trigger" id="lftCatTrigger">
                <span class="ksd-trigger-text placeholder" id="lftCatLabel">Choose subcategory…</span>
                <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="ksd-dropdown" id="lftCatDropdown">
                <div class="ksd-search-row"><input type="text" class="ksd-search" id="lftCatSearch" placeholder="Search category…" autocomplete="off"></div>
                <div class="ksd-list" id="lftCatList">
                    @php $catsByParent = $categories->groupBy('parent_id'); @endphp
                    @foreach($catsByParent as $parentId => $subs)
                    @php $parentName = optional($subs->first()->parent)->name ?? 'Other'; @endphp
                    <div class="ksd-group-label">{{ strtoupper($parentName) }}</div>
                    @foreach($subs as $sub)
                    <div class="ksd-item ksd-item-sub" data-value="{{ $sub->id }}" data-label="{{ $sub->name }}" data-search="{{ strtolower($parentName.' '.$sub->name) }}">{{ $sub->name }}</div>
                    @endforeach
                    @endforeach
                    <div class="ksd-empty k-hidden" id="lftCatEmpty">No categories match</div>
                </div>
            </div>
        </div>
    </div>

    <div class="lf-step-area" id="lftStep2">
        <div class="lf-step-row lf-step-row--mb">
            <span class="lf-step-num">2</span>
            <span class="lf-step-lbl">Tick the fields to show in this category</span>
            <span id="lftLoadingBadge" class="lf-loading-badge k-hidden">Loading…</span>
        </div>
        <div id="lftNoCatMsg" class="lf-placeholder-msg">↑ Select a subcategory above to load its assigned fields</div>
        <div class="lf-cb-grid k-hidden" id="lftFieldCheckboxes">
            @foreach($fields as $field)
            @php $fo = $typeOptions[$field->type] ?? ['label'=>$field->type,'cls'=>'lft-type-text']; @endphp
            <label class="lf-cb-item">
                <input type="checkbox" name="field_ids[]" value="{{ $field->id }}" class="assign-cb">
                <span>{{ $field->label }}</span>
                <span class="lf-cb-type lf-type {{ $fo['cls'] }}">{{ $fo['label'] }}</span>
            </label>
            @endforeach
        </div>
        @if($fields->isEmpty())
        <div class="lf-no-fields-warn">No custom fields defined yet. <a href="?tab=fields" class="lf-link-green">Add fields first →</a></div>
        @endif
    </div>

    <div class="lf-form-foot lf-save-foot">
        <button type="submit" class="lf-btn-primary" id="lftSaveBtn">💾 Save Assignment</button>
        <span id="lftSaveHint" class="lf-save-hint">Select a category first</span>
    </div>
    </form>
</div>

<div class="lf-card">
    <div class="lf-card-head">
        <div class="lf-card-icon blue">≡</div>
        <span class="lf-card-title">Current Assignments</span>
        <span class="lf-card-meta">{{ $categories->filter(fn($c)=>$c->customFields->count())->count() }} categories assigned</span>
    </div>
    <div class="lf-table-wrap">
    <table class="lf-table">
    <thead><tr>
        <th>Subcategory</th><th>Parent Category</th><th>Assigned Fields</th><th>Actions</th>
    </tr></thead>
    <tbody>
    @php $anyAssigned = false; @endphp
    @foreach($categories as $sub)
    @php $subFields = $sub->customFields; @endphp
    @if($subFields->count())
    @php $anyAssigned = true; @endphp
    <tr>
        <td><b>{{ $sub->name }}</b></td>
        <td><span class="lf-parent-text">{{ optional($sub->parent)->name }}</span></td>
        <td>@foreach($subFields as $sf)<span class="lf-field-tag">{{ $sf->label }}</span>@endforeach</td>
        <td><a href="?tab=assign&cat={{ $sub->id }}" class="lf-act">Edit</a></td>
    </tr>
    @endif
    @endforeach
    @if(!$anyAssigned)
    <tr><td colspan="4" class="lf-no-assign">
        <div class="lf-no-assign-msg">No assignments yet — use the form above to link fields to categories.</div>
        <div class="lf-no-assign-sub">Fields assigned here appear on the listing form when a user picks that category.</div>
    </td></tr>
    @endif
    </tbody>
    </table>
    </div>
</div>
@endif

</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Add-form toggle ───────────────────────────────────────────────────────
function lftToggleAddForm(forceOpen) {
    var body    = document.getElementById('lftAddFormBody');
    var label   = document.getElementById('lftAddToggleLabel');
    var chevron = document.getElementById('lftAddChevron');
    var isOpen  = !body.classList.contains('k-hidden');
    if (forceOpen !== undefined) isOpen = !forceOpen;
    body.classList.toggle('k-hidden', isOpen);
    label.textContent = isOpen ? 'Expand' : 'Collapse';
    chevron.classList.toggle('open', !isOpen);
}
(function(){
    var head = document.getElementById('lftAddCardHead');
    if (head) head.addEventListener('click', lftToggleAddForm);
    var cancel = document.getElementById('lftCancelAddBtn');
    if (cancel) cancel.addEventListener('click', lftToggleAddForm);
    var addFirst = document.getElementById('lftAddFirstBtn');
    if (addFirst) addFirst.addEventListener('click', function(){ lftToggleAddForm(true); });
})();

// ── Search / filter / pagination ──────────────────────────────────────────
var lftCurrentPage = 1, lftPerPageVal = 10, lftActiveType = '', lftFilteredRows = [];

(function(){
    var search     = document.getElementById('lftSearch');
    var clear      = document.getElementById('lftSearchClear');
    var clearSearch= document.getElementById('lftClearSearchBtn');
    var prev       = document.getElementById('lftPrevBtn');
    var next       = document.getElementById('lftNextBtn');
    var perPg      = document.getElementById('lftPerPage');
    if (search)      search.addEventListener('input', lftApplyFilters);
    if (clear)       clear.addEventListener('click', function(){ if(search) search.value=''; lftApplyFilters(); });
    if (clearSearch) clearSearch.addEventListener('click', function(){ if(search) search.value=''; lftApplyFilters(); });
    if (prev)        prev.addEventListener('click', function(){ lftChangePage(-1); });
    if (next)        next.addEventListener('click', function(){ lftChangePage(1); });
    if (perPg)       perPg.addEventListener('change', function(){ lftPerPageVal=parseInt(this.value)||10; lftCurrentPage=1; lftRender(); });
})();

function lftApplyFilters() {
    var q     = (document.getElementById('lftSearch')?.value||'').toLowerCase().trim();
    var clr   = document.getElementById('lftSearchClear');
    if (clr) clr.classList.toggle('k-hidden', !q);
    var all   = Array.from(document.querySelectorAll('#lftFieldsTbody .lft-data-row'));
    lftFilteredRows = all.filter(function(r){
        if (lftActiveType && r.dataset.type !== lftActiveType) return false;
        if (!q) return true;
        return (r.dataset.label||'').indexOf(q)!==-1 || (r.dataset.key||'').indexOf(q)!==-1;
    });
    var badge = document.getElementById('lftCountBadge');
    if (badge) badge.textContent = lftFilteredRows.length===all.length ? all.length+' fields' : lftFilteredRows.length+' of '+all.length+' fields';
    lftCurrentPage = 1;
    lftRender();
}

function lftRender() {
    var all  = Array.from(document.querySelectorAll('#lftFieldsTbody .lft-data-row'));
    var edit = Array.from(document.querySelectorAll('#lftFieldsTbody tr[id^="lft-edit-"]'));
    all.forEach(function(r){ r.classList.add('k-hidden'); });
    edit.forEach(function(r){ r.classList.add('k-hidden'); });
    var start = (lftCurrentPage-1)*lftPerPageVal;
    lftFilteredRows.slice(start, start+lftPerPageVal).forEach(function(r){ r.classList.remove('k-hidden'); });
    var noRes = document.getElementById('lftNoResults');
    if (noRes) noRes.classList.toggle('k-hidden', lftFilteredRows.length!==0);
    var total = Math.max(1, Math.ceil(lftFilteredRows.length/lftPerPageVal));
    var info  = document.getElementById('lftPageInfo');
    var nums  = document.getElementById('lftPageNumbers');
    var prev  = document.getElementById('lftPrevBtn');
    var next  = document.getElementById('lftNextBtn');
    var pager = document.getElementById('lftPagination');
    if (pager) pager.classList.toggle('k-hidden', lftFilteredRows.length<=lftPerPageVal && total<=1);
    if (info) { var s=start+1,e=Math.min(start+lftPerPageVal,lftFilteredRows.length); info.textContent=lftFilteredRows.length?'Showing '+s+'–'+e+' of '+lftFilteredRows.length:''; }
    if (prev) prev.disabled=lftCurrentPage<=1;
    if (next) next.disabled=lftCurrentPage>=total;
    if (nums) {
        nums.innerHTML='';
        var mx=5,sp=Math.max(1,lftCurrentPage-Math.floor(mx/2)),ep=Math.min(total,sp+mx-1);
        if(ep-sp<mx-1) sp=Math.max(1,ep-mx+1);
        for(var p=sp;p<=ep;p++)(function(pg){
            var btn=document.createElement('button'); btn.type='button';
            btn.className='lf-page-num'+(pg===lftCurrentPage?' active':'');
            btn.textContent=pg;
            btn.addEventListener('click',function(){ lftCurrentPage=pg; lftRender(); });
            nums.appendChild(btn);
        })(p);
    }
}
function lftChangePage(dir){
    lftCurrentPage=Math.max(1,Math.min(lftCurrentPage+dir,Math.ceil(lftFilteredRows.length/lftPerPageVal)));
    lftRender();
}

document.querySelectorAll('.lf-fpill').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('.lf-fpill').forEach(function(b){ b.classList.remove('active'); });
        this.classList.add('active');
        lftActiveType = this.dataset.type||'';
        lftApplyFilters();
    });
});
lftApplyFilters();

// ── ksd dropdown for type fields ──────────────────────────────────────────
function makeTypeKsd(triggerId, dropdownId, hiddenId, labelId) {
    var trigger  = document.getElementById(triggerId);
    var dropdown = document.getElementById(dropdownId);
    var hidden   = document.getElementById(hiddenId);
    var labelEl  = document.getElementById(labelId);
    if (!trigger || !dropdown) return;
    var open = false;
    function openD()  { dropdown.classList.add('ksd-open'); trigger.classList.add('ksd-open'); open=true; }
    function closeD() { dropdown.classList.remove('ksd-open'); trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click', function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click', function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    dropdown.querySelectorAll('.ksd-item').forEach(function(item){
        item.addEventListener('click', function(){
            if (hidden) hidden.value=this.dataset.value;
            if (labelEl) labelEl.innerHTML=this.innerHTML;
            dropdown.querySelectorAll('.ksd-item').forEach(function(i){ i.classList.remove('ksd-selected'); });
            this.classList.add('ksd-selected');
            closeD();
        });
    });
}
makeTypeKsd('lftAddTypeTrigger','lftAddTypeDropdown','lftAddTypeVal','lftAddTypeLabel');

(function(){
    var typeHidden=document.getElementById('lftAddTypeVal');
    var optWrap=document.getElementById('lftAddOptionsWrap');
    var unitWrap=document.getElementById('lftAddUnitWrap');
    var multiWrap=document.getElementById('lftAddMultiWrap');
    var fullWrap=document.getElementById('lftAddFullWrap');
    if(!typeHidden) return;
    var withOpts=['select','checkbox_group','pill_group'], withUnit=['text_unit'], withMulti=['pill_group'];
    function upd(){ var t=typeHidden.value;
        if(optWrap) optWrap.classList.toggle('k-hidden',withOpts.indexOf(t)===-1);
        if(unitWrap) unitWrap.classList.toggle('k-hidden',withUnit.indexOf(t)===-1);
        if(multiWrap) multiWrap.classList.toggle('k-hidden',withMulti.indexOf(t)===-1);
        if(fullWrap) fullWrap.classList.toggle('k-hidden',t==='brand_select'||t==='model_select');
    }
    new MutationObserver(upd).observe(typeHidden,{attributes:true,attributeFilter:['value']});
    upd();
})();

@foreach($fields as $field)
makeTypeKsd('lftTypeTrigger-{{ $field->id }}','lftTypeDropdown-{{ $field->id }}','lftType-{{ $field->id }}','lftTypeLabel-{{ $field->id }}');
@endforeach

// ── Inline edit toggle ────────────────────────────────────────────────────
document.addEventListener('click', function(e){
    var editBtn = e.target.closest('.lft-edit-btn');
    if (editBtn) {
        var id = editBtn.dataset.fid;
        var row = document.getElementById('lft-edit-'+id);
        var isOpen = !row.classList.contains('k-hidden');
        document.querySelectorAll('[id^="lft-edit-"]').forEach(function(r){ r.classList.add('k-hidden'); });
        row.classList.toggle('k-hidden', isOpen);
        if (!isOpen) row.scrollIntoView({behavior:'smooth',block:'nearest'});
        return;
    }
    var cancelBtn = e.target.closest('.lft-cancel-edit-btn');
    if (cancelBtn) { document.getElementById('lft-edit-'+cancelBtn.dataset.fid).classList.add('k-hidden'); }
});

document.querySelectorAll('.lft-del-form').forEach(function(form){
    form.addEventListener('submit', function(e){
        if (!confirm('Delete \''+( this.dataset.label||'this field')+'\'?')) e.preventDefault();
    });
});

// ── Category ksd (assign tab) ─────────────────────────────────────────────
(function(){
    var trigger  = document.getElementById('lftCatTrigger'); if(!trigger) return;
    var dropdown = document.getElementById('lftCatDropdown');
    var hidden   = document.getElementById('lftCatNative');
    var labelEl  = document.getElementById('lftCatLabel');
    var searchEl = document.getElementById('lftCatSearch');
    var list     = document.getElementById('lftCatList');
    var emptyEl  = document.getElementById('lftCatEmpty');
    var open = false;
    function getItems(){ return list.querySelectorAll('.ksd-item'); }
    function openD()  { dropdown.classList.add('ksd-open'); trigger.classList.add('ksd-open'); searchEl.value=''; filterItems(''); searchEl.focus(); open=true; }
    function closeD() { dropdown.classList.remove('ksd-open'); trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click', function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click', function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    searchEl.addEventListener('input', function(){ filterItems(this.value.toLowerCase()); });
    list.addEventListener('click', function(e){
        var item=e.target.closest('.ksd-item'); if(!item) return;
        var val=item.dataset.value, lbl=item.dataset.label;
        hidden.value=val; labelEl.textContent=lbl; labelEl.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
        getItems().forEach(function(i){ i.classList.remove('ksd-selected'); }); item.classList.add('ksd-selected');
        closeD();
        var title=document.getElementById('lftCardTitle'); if(title) title.textContent='Fields for: '+lbl;
        loadAssigned(val);
    });
    function filterItems(q){ var any=false; getItems().forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.classList.toggle('k-hidden',!m); if(m)any=true; }); if(emptyEl) emptyEl.classList.toggle('k-hidden',any); }
    var urlCat = new URLSearchParams(window.location.search).get('cat');
    if(urlCat){ var pre=Array.from(getItems()).find(function(i){ return i.dataset.value===urlCat; }); if(pre){ hidden.value=urlCat; labelEl.textContent=pre.dataset.label; labelEl.classList.remove('placeholder'); trigger.classList.add('ksd-has-value'); pre.classList.add('ksd-selected'); loadAssigned(urlCat); } }
})();

function loadAssigned(catId){
    var loading=document.getElementById('lftLoadingBadge');
    var noCat  =document.getElementById('lftNoCatMsg');
    var cbGrid =document.getElementById('lftFieldCheckboxes');
    var hint   =document.getElementById('lftSaveHint');
    var count  =document.getElementById('lftAssignedCount');
    if(noCat)  noCat.classList.add('k-hidden');
    if(cbGrid) cbGrid.classList.remove('k-hidden');
    if(loading)loading.classList.remove('k-hidden');
    if(hint)   hint.classList.add('k-hidden');
    fetch('/admin/listing-fields/category-fields/'+catId)
        .then(function(r){ return r.json(); })
        .then(function(data){
            if(loading) loading.classList.add('k-hidden');
            var ids=(data.assigned||[]).map(Number);
            var checked=0;
            document.querySelectorAll('.assign-cb').forEach(function(cb){
                cb.checked=ids.indexOf(parseInt(cb.value))!==-1;
                if(cb.checked) checked++;
                var item=cb.closest('.lf-cb-item'); if(item) item.classList.toggle('assigned',cb.checked);
            });
            if(count){ count.textContent=checked+' field'+(checked===1?'':'s')+' assigned'; count.classList.remove('k-hidden'); }
        })
        .catch(function(){ if(loading) loading.classList.add('k-hidden'); });
}

document.querySelectorAll('.assign-cb').forEach(function(cb){
    cb.addEventListener('change', function(){
        var item=this.closest('.lf-cb-item'); if(item) item.classList.toggle('assigned',this.checked);
        var checked=document.querySelectorAll('.assign-cb:checked').length;
        var count=document.getElementById('lftAssignedCount');
        if(count&&!count.classList.contains('k-hidden')) count.textContent=checked+' field'+(checked===1?'':'s')+' assigned';
    });
});
</script>
@endpush
