{{-- CSP blocks inline style="" attributes, so style="display:none" never hid anything.
     Re-apply just that declaration through the CSSOM (allowed) while the page parses. --}}
<script nonce="{{ $cspNonce ?? '' }}">
(function(){var re=/(^|;)\s*display\s*:\s*none\s*(;|$)/i;
function fix(el){if(el.nodeType===1&&el.hasAttribute('style')&&re.test(el.getAttribute('style')))el.style.display='none';}
var mo=new MutationObserver(function(ms){for(var i=0;i<ms.length;i++){var n=ms[i].addedNodes;for(var j=0;j<n.length;j++){fix(n[j]);if(n[j].querySelectorAll){var c=n[j].querySelectorAll('[style]');for(var k=0;k<c.length;k++)fix(c[k]);}}}});
mo.observe(document.documentElement,{childList:true,subtree:true});
document.addEventListener('DOMContentLoaded',function(){var a=document.querySelectorAll('[style]');for(var i=0;i<a.length;i++)fix(a[i]);setTimeout(function(){mo.disconnect();},0);});})();
</script>
