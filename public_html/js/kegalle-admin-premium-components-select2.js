document.addEventListener('DOMContentLoaded', function () {
if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
jQuery('select').each(function () {
const $select = jQuery(this);
if ($select.hasClass('select2-hidden-accessible')) return;
if ($select.hasClass('no-select2')) return;
if ($select.closest('.kaa-filter-bar, .kn-filter-wrap').length) return;
const ph = $select.data('placeholder') || $select.attr('placeholder') || 'Select option';
$select.select2({
width: '100%',
minimumResultsForSearch: 6,
placeholder: ph,
allowClear: !!$select.data('placeholder'),
});
});
}
document.querySelectorAll('.sa-actions-inline a, .ka-actions-inline a').forEach((el) => {
const txt = (el.textContent || '').trim() || 'View';
if (!el.getAttribute('title')) el.setAttribute('title', txt);
if (!el.getAttribute('aria-label')) el.setAttribute('aria-label', txt);
el.setAttribute('data-ka-tip', txt);
});
document.querySelectorAll('.sa-actions-inline button, .ka-actions-inline button').forEach((el) => {
const txt = (el.textContent || '').trim() || 'Action';
if (!el.getAttribute('title')) el.setAttribute('title', txt);
if (!el.getAttribute('aria-label')) el.setAttribute('aria-label', txt);
el.setAttribute('data-ka-tip', txt);
});
document.querySelectorAll('.sa-table-wrap, .ka-table-wrap').forEach((wrap) => {
wrap.setAttribute('tabindex', '0');
});
});