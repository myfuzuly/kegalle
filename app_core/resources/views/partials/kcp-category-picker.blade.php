{{--
  kcp-category-picker — fancy hierarchical browse+search category picker
  Props (pass via @include or component):
    $kcpNs          string   JS/DOM namespace, e.g. 'kcp' (default). Use unique names per page.
    $kcpHiddenId    string   id of the <input type="hidden"> to store the category_id value
    $kcpCategories  Collection  full category collection
    $kcpSelectedId  string|int  pre-selected category id (for edit forms)
    $kcpOldKey      string   Laravel old() key (default 'category_id')
    $kcpOnChange    string   JS snippet called with (id) when a category is selected
--}}
@php
$_ns       = $kcpNs       ?? 'kcp';
$_hidId    = $kcpHiddenId ?? 'kcp-cat-val';
$_oldKey   = $kcpOldKey   ?? 'category_id';
$_cats     = $kcpCategories;
$_onChange = $kcpOnChange ?? '';
$_selId    = old($_oldKey, $kcpSelectedId ?? '');

// Build tree array
$_tree = [];
foreach ($_cats->whereNull('parent_id') as $_m) {
    $_subs = [];
    foreach ($_cats->where('parent_id', $_m->id) as $_s) {
        $_leaves = [];
        foreach ($_cats->where('parent_id', $_s->id) as $_l) {
            $_leaves[] = ['id' => $_l->id, 'name' => $_l->name];
        }
        $_subs[] = ['id' => $_s->id, 'name' => $_s->name, 'leaves' => $_leaves];
    }
    $_tree[] = ['id' => $_m->id, 'name' => $_m->name, 'icon' => $_m->icon ?: '🛒', 'subs' => $_subs];
}

// Resolve label for selected value (edit forms)
$_selLabel = '';
$_selPath  = '';
$_selIcon  = '📂';
if ($_selId) {
    $_cat = $_cats->firstWhere('id', $_selId);
    if ($_cat) {
        $_par  = $_cat->parent_id  ? $_cats->firstWhere('id', $_cat->parent_id)  : null;
        $_gpar = $_par && $_par->parent_id ? $_cats->firstWhere('id', $_par->parent_id) : null;
        $_selLabel = $_cat->name;
        if ($_gpar)     $_selPath = $_gpar->name . ' › ' . $_par->name . ' › ' . $_cat->name;
        elseif ($_par)  $_selPath = $_par->name . ' › ' . $_cat->name;
        else            $_selPath = $_cat->name;
        $_root = $_gpar ?? $_par ?? $_cat;
        $_selIcon = $_root->icon ?: '📂';
    }
}
@endphp

{{-- CSS — only output once per page --}}
@unless(isset($GLOBALS['_kcpCssOut']))
@php $GLOBALS['_kcpCssOut'] = true; @endphp

@endunless

{{-- Hidden input keeps the category_id value --}}
<input type="hidden" name="{{ $_oldKey }}" id="{{ $_hidId }}" value="{{ $_selId }}">

{{-- Picker widget --}}
<div id="{{ $_ns }}Picker" class="kcp-wrap">
    <button type="button" id="{{ $_ns }}Trigger" class="kcp-trigger {{ $_selLabel ? 'kcp-has-value' : '' }}">
        <span id="{{ $_ns }}TIcon" class="kcp-t-icon">{{ $_selIcon }}</span>
        <span class="kcp-t-text">
            <span id="{{ $_ns }}TLabel" class="kcp-t-name">{{ $_selLabel ?: 'Select a category…' }}</span>
            <span id="{{ $_ns }}TPath" class="kcp-t-path"@if(!$_selPath || $_selLabel === $_selPath) style="display:none"@endif>{{ $_selPath }}</span>
        </span>
        <span id="{{ $_ns }}TClear" class="kcp-t-clear" role="button" tabindex="0"
              title="Clear"
              @if(!$_selLabel) style="display:none" @endif>×</span>
        <svg class="kcp-t-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
    </button>
    <div id="{{ $_ns }}Dropdown" class="kcp-dropdown" style="display:none">
        <div class="kcp-search-row">
            <input type="text" id="{{ $_ns }}SearchInput" class="kcp-search-input"
                   placeholder="Search categories…"
                   autocomplete="off">
        </div>
        <div id="{{ $_ns }}SearchRes" class="kcp-results" style="display:none"></div>
        <div id="{{ $_ns }}Browse">
            <div id="{{ $_ns }}Breadcrumb" class="kcp-breadcrumb" style="display:none">
                <button type="button" id="{{ $_ns }}BackBtn" class="kcp-back-btn">‹ Back</button>
                <span id="{{ $_ns }}BreadcrumbLabel" class="kcp-breadcrumb-label"></span>
            </div>
            <div id="{{ $_ns }}List" class="kcp-list"></div>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var NS      = '{{ $_ns }}';
    var HID_ID  = '{{ $_hidId }}';
    var ON_CHANGE = function(id){ {!! $_onChange ?? '' !!} };
    window[NS + 'CatTree'] = @json($_tree);
    var _level='main', _main=null, _sub=null;
    function $id(s){ return document.getElementById(NS+s); }
    function $hid(){ return document.getElementById(HID_ID); }

    function _esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function _kcpOpen(){
        var d=$id('Dropdown'),t=$id('Trigger');
        d.style.display=''; t.classList.add('kcp-open');
        var i=$id('SearchInput'); if(i){i.value='';_search('');i.focus();}
        _renderList();
    }
    function _kcpClose(){
        $id('Dropdown').style.display='none';
        $id('Trigger').classList.remove('kcp-open');
    }
    window[NS+'Toggle'] = function(e){ e&&e.stopPropagation(); $id('Dropdown').style.display==='none'?_kcpOpen():_kcpClose(); };
    window[NS+'Back']   = function(){
        var i=$id('SearchInput'); if(i){i.value='';_search('');}
        if(_level==='leaf'){_level='sub';_renderList();}else{_level='main';_main=null;_sub=null;_renderList();}
    };
    window[NS+'Reset']  = function(e){
        e&&e.stopPropagation();
        _level='main';_main=null;_sub=null;
        var h=$hid(); if(h){h.value='';h.dispatchEvent(new Event('change'));}
        _setTrigger(null,null,null);
        ON_CHANGE('');
        _kcpClose();
    };

    function _setTrigger(icon,name,path){
        $id('TIcon').textContent=icon||'📂';
        $id('TLabel').textContent=name||'Select a category…';
        var p=$id('TPath'),c=$id('TClear'),t=$id('Trigger');
        if(path&&path!==name){p.textContent=path;p.style.display='';}else{p.style.display='none';}
        if(name){c.style.display='';t.classList.add('kcp-has-value');t.style.color='';}
        else{c.style.display='none';t.classList.remove('kcp-has-value');t.style.color='';}
    }

    function _select(id,name,path,icon){
        var h=$hid(); if(h){h.value=id;h.dispatchEvent(new Event('change'));}
        _setTrigger(icon||'📂',name,path);
        ON_CHANGE(id);
        _kcpClose();
        _level='main';
    }

    function _hl(text, q){
        if(!q) return _esc(text);
        var lo=text.toLowerCase(), qi=q.toLowerCase(), idx=lo.indexOf(qi);
        if(idx===-1) return _esc(text);
        return _esc(text.slice(0,idx))+'<mark>'+_esc(text.slice(idx,idx+q.length))+'</mark>'+_esc(text.slice(idx+q.length));
    }

    var _kbIdx=-1, _kbItems=[];
    function _kbReset(){ _kbIdx=-1; _kbItems=[]; }
    function _kbBind(container){
        _kbItems=Array.from(container.querySelectorAll('.kcp-res-item,.kcp-main-card,.kcp-sub-item,.kcp-leaf-item'));
        _kbIdx=-1;
    }

    function _renderList(){
        var tree=window[NS+'CatTree']||[];
        var bc=$id('Breadcrumb'),lEl=$id('List');
        lEl.innerHTML=''; _kbReset();
        if(_level==='main'){
            bc.style.display='none';
            var grid=document.createElement('div'); grid.className='kcp-main-grid';
            tree.forEach(function(m){
                var r=document.createElement('div'); r.className='kcp-main-card';
                var subCount=(m.subs||[]).reduce(function(n,s){return n+(s.leaves&&s.leaves.length?s.leaves.length:1);},0);
                r.innerHTML='<span class="kcp-mc-icon">'+_esc(m.icon||'🛒')+'</span>'
                    +'<span class="kcp-mc-text"><span class="kcp-mc-name">'+_esc(m.name)+'</span>'
                    +(subCount?'<span class="kcp-mc-count">'+subCount+' items</span>':'')
                    +'</span>'
                    +(m.subs.length?'<span class="kcp-mc-arrow">›</span>':'');
                r.addEventListener('click',function(){
                    if(!m.subs.length){_select(m.id,m.name,m.name,m.icon);return;}
                    _level='sub';_main=m;_renderList();
                });
                grid.appendChild(r);
            });
            lEl.appendChild(grid);
            _kbBind(lEl);
        } else if(_level==='sub'){
            bc.style.display='flex';
            $id('BreadcrumbLabel').textContent=(_main.icon||'')+' '+_main.name;
            var allRow=document.createElement('div'); allRow.className='kcp-sub-item';
            allRow.innerHTML='<span class="kcp-si-dot" style="background:#94a3b8"></span><span class="kcp-si-name" style="color:#64748b">All '+_esc(_main.name)+'</span>';
            allRow.addEventListener('click',function(){ _select(_main.id,'All '+_main.name,_main.name,_main.icon); });
            lEl.appendChild(allRow);
            _main.subs.forEach(function(s){
                var r=document.createElement('div'); r.className='kcp-sub-item';
                r.innerHTML='<span class="kcp-si-dot"></span><span class="kcp-si-name">'+_esc(s.name)+'</span>'
                    +(s.leaves.length?'<span class="kcp-si-arrow">›</span>':'');
                r.addEventListener('click',function(){
                    if(!s.leaves.length){_select(s.id,s.name,_main.name+' › '+s.name,_main.icon);return;}
                    _level='leaf';_sub=s;_renderList();
                });
                lEl.appendChild(r);
            });
            _kbBind(lEl);
        } else {
            bc.style.display='flex';
            $id('BreadcrumbLabel').textContent=(_main.icon||'')+' '+_main.name+' › '+_sub.name;
            var cl=document.createElement('div'); cl.className='kcp-leaf-wrap';
            var allLeaf=document.createElement('div'); allLeaf.className='kcp-leaf-item';
            allLeaf.textContent='All '+_sub.name;
            allLeaf.addEventListener('click',function(){
                cl.querySelectorAll('.kcp-leaf-item').forEach(function(x){x.classList.remove('kcp-leaf-sel');});
                allLeaf.classList.add('kcp-leaf-sel');
                _select(_sub.id,'All '+_sub.name,_main.name+' › '+_sub.name,_main.icon);
            });
            cl.appendChild(allLeaf);
            _sub.leaves.forEach(function(l){
                var t=document.createElement('div'); t.className='kcp-leaf-item';
                t.textContent=l.name;
                t.addEventListener('click',function(){
                    cl.querySelectorAll('.kcp-leaf-item').forEach(function(x){x.classList.remove('kcp-leaf-sel');});
                    t.classList.add('kcp-leaf-sel');
                    _select(l.id,l.name,_main.name+' › '+_sub.name+' › '+l.name,_main.icon);
                });
                cl.appendChild(t);
            });
            lEl.appendChild(cl);
            _kbBind(lEl);
        }
    }

    function _search(q){
        var rEl=$id('SearchRes'),br=$id('Browse');
        var raw=q; q=(q||'').trim().toLowerCase();
        if(!q){rEl.style.display='none';rEl.innerHTML='';br.style.display='';_kbReset();return;}
        br.style.display='none';
        var tree=window[NS+'CatTree']||[];
        var hits=[]; var seen={};
        function nScore(name){
            var n=name.toLowerCase();
            if(n===q) return 0;
            if(n.indexOf(q)===0) return 1;
            if(n.indexOf(q)>-1) return 2;
            return 99;
        }
        // Tier*100 + nameScore — lower = better
        // Tier 0: main name matches
        // Tier 1: sub name matches
        // Tier 2: leaf name matches
        // Tier 3: path-only matches (sub or main name matches, own name doesn't)
        tree.forEach(function(m){
            var ms=nScore(m.name);
            if(!(m.subs||[]).length){
                if(ms<99) hits.push({id:m.id,name:m.name,path:m.name,icon:m.icon,score:ms});
                return;
            }
            if(ms<99) hits.push({id:m.id,name:m.name,path:m.name,icon:m.icon,score:ms});
            (m.subs||[]).forEach(function(s){
                var ss=nScore(s.name);
                if(!(s.leaves||[]).length){
                    if(ss<99) hits.push({id:s.id,name:s.name,path:m.name+' › '+s.name,icon:m.icon,score:100+ss});
                    else if(ms<99) hits.push({id:s.id,name:s.name,path:m.name+' › '+s.name,icon:m.icon,score:300+ms});
                    return;
                }
                if(ss<99) hits.push({id:s.id,name:s.name,path:m.name+' › '+s.name,icon:m.icon,score:100+ss});
                else if(ms<99) hits.push({id:s.id,name:s.name,path:m.name+' › '+s.name,icon:m.icon,score:300+ms});
                (s.leaves||[]).forEach(function(l){
                    var ls=nScore(l.name);
                    if(ls<99) hits.push({id:l.id,name:l.name,path:m.name+' › '+s.name+' › '+l.name,icon:m.icon,score:200+ls});
                    else if(ss<99) hits.push({id:l.id,name:l.name,path:m.name+' › '+s.name+' › '+l.name,icon:m.icon,score:300+ss});
                    else if(ms<99) hits.push({id:l.id,name:l.name,path:m.name+' › '+s.name+' › '+l.name,icon:m.icon,score:300+ms+1});
                });
            });
        });
        hits=hits.filter(function(h){var k=h.id+'|'+h.path;if(seen[k])return false;seen[k]=true;return true;});
        hits.sort(function(a,b){return a.score-b.score;});
        rEl.innerHTML='';
        if(!hits.length){
            rEl.innerHTML='<div class="kcp-res-empty">No results for "'+_esc(raw)+'"</div>';
        } else {
            hits.slice(0,40).forEach(function(h){
                var el=document.createElement('div'); el.className='kcp-res-item';
                el.innerHTML='<span class="kcp-res-icon">'+_esc(h.icon||'📂')+'</span>'
                    +'<span class="kcp-res-body"><span class="kcp-res-name">'+_hl(h.name,q)+'</span>'
                    +'<span class="kcp-res-path">'+_hl(h.path,q)+'</span></span>';
                el.addEventListener('click',function(){ _select(h.id,h.name,h.path,h.icon); });
                rEl.appendChild(el);
            });
            _kbBind(rEl);
        }
        rEl.style.display='';
    }
    window[NS+'Search'] = _search;

    var _si=$id('SearchInput');
    if(_si){
        _si.addEventListener('input',function(){ _search(this.value); });
        _si.addEventListener('keydown',function(e){
            if(e.key==='ArrowDown'||e.key==='ArrowUp'){
                e.preventDefault();
                if(!_kbItems.length) return;
                _kbItems.forEach(function(x){x.style.outline='';});
                if(e.key==='ArrowDown') _kbIdx=Math.min(_kbIdx+1,_kbItems.length-1);
                else _kbIdx=Math.max(_kbIdx-1,0);
                var cur=_kbItems[_kbIdx];
                cur.style.outline='2px solid #1b5e20';
                cur.style.outlineOffset='-2px';
                cur.scrollIntoView({block:'nearest'});
            } else if(e.key==='Enter'&&_kbIdx>=0&&_kbItems[_kbIdx]){
                e.preventDefault(); e.stopPropagation();
                _kbItems[_kbIdx].click();
            } else if(e.key==='Escape'){ _kcpClose(); }
        });
    }

    // Wire trigger, clear, back via addEventListener (CSP nonce blocks inline onclick attrs)
    var _tr=$id('Trigger'); if(_tr) _tr.addEventListener('click',function(e){e.stopPropagation();$id('Dropdown').style.display==='none'?_kcpOpen():_kcpClose();});
    var _cl=$id('TClear'); if(_cl) _cl.addEventListener('click',function(e){e.stopPropagation();_level='main';_main=null;_sub=null;var h=$hid();if(h){h.value='';h.dispatchEvent(new Event('change'));}ON_CHANGE('');_setTrigger(null,null,null);_kcpClose();});
    var _bb=$id('BackBtn'); if(_bb) _bb.addEventListener('click',function(){var i=$id('SearchInput');if(i){i.value='';_search('');}if(_level==='leaf'){_level='sub';_renderList();}else{_level='main';_main=null;_sub=null;_renderList();}});

    // Close on outside click
    document.addEventListener('mousedown',function(e){
        var p=$id('Picker');
        if(p&&!p.contains(e.target)) _kcpClose();
    });

    // Restore selection display if already has value
    @if($_selId && $_selLabel)
    _setTrigger({!! json_encode($_selIcon, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) !!},{!! json_encode($_selLabel, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) !!},{!! json_encode($_selPath, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) !!});
    @endif

    _renderList();
})();
</script>
@endpush
