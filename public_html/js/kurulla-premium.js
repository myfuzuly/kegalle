document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[data-mobile-menu]').forEach(function(btn){
    btn.addEventListener('click',function(){
      var target=document.querySelector(btn.getAttribute('data-mobile-menu'));
      if(target){target.classList.toggle('is-open')}
    });
  });
  document.querySelectorAll('.listing-card,.store-card,.category-card').forEach(function(card){
    card.addEventListener('mouseenter',function(){card.style.willChange='transform'});
    card.addEventListener('mouseleave',function(){card.style.willChange='auto'});
  });
});
