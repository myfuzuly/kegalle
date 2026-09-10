document.addEventListener('DOMContentLoaded', () => {
document.querySelectorAll('.ka-table-wrap, .sa-table-wrap').forEach(wrap => {
wrap.addEventListener('wheel', (e) => {
if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) return;
}, { passive: true });
});
});