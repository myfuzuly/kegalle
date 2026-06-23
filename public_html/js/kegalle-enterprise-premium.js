document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-ep-save]').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const id = btn.getAttribute('data-ep-save');
      const saved = JSON.parse(localStorage.getItem('kg_saved') || '[]');
      if (!saved.includes(id)) saved.push(id);
      localStorage.setItem('kg_saved', JSON.stringify(saved));
      btn.classList.add('saved');
      btn.textContent = 'Saved';
    });
  });

  document.querySelectorAll('.kg-listing-card[href]').forEach(card => {
    card.addEventListener('click', () => {
      const recent = JSON.parse(localStorage.getItem('kg_recent') || '[]');
      const item = {url: card.getAttribute('href'), title: card.querySelector('h3')?.textContent || 'Listing'};
      const filtered = recent.filter(x => x.url !== item.url);
      filtered.unshift(item);
      localStorage.setItem('kg_recent', JSON.stringify(filtered.slice(0,8)));
    });
  });
});
