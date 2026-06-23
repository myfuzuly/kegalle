(function(){'use strict';
function qs(s,c=document){return c.querySelector(s)}function qsa(s,c=document){return Array.from(c.querySelectorAll(s))}
const menu=qs('[data-km-links]'), btn=qs('[data-km-menu]');
if(menu&&btn){btn.addEventListener('click',e=>{e.preventDefault();menu.classList.toggle('open');btn.classList.toggle('active')});document.addEventListener('click',e=>{if(menu.classList.contains('open')&&!menu.contains(e.target)&&!btn.contains(e.target)){menu.classList.remove('open');btn.classList.remove('active')}})}
qsa('[data-thumb]').forEach(img=>img.addEventListener('click',()=>{const main=qs('[data-main-image]');if(main){main.src=img.src;qsa('[data-thumb]').forEach(x=>x.classList.remove('active'));img.classList.add('active')}}));
qsa('[data-share]').forEach(btn=>btn.addEventListener('click',async e=>{e.preventDefault();try{if(navigator.share){await navigator.share({title:document.title,url:location.href})}else if(navigator.clipboard){await navigator.clipboard.writeText(location.href);alert('Link copied')}}catch(err){console.log(err)}}));
qsa('img').forEach(img=>{if(img.complete)img.classList.add('loaded');img.addEventListener('load',()=>img.classList.add('loaded'))});
})();
