document.addEventListener('DOMContentLoaded', function () {
if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
jQuery('select').each(function () {
const $select = jQuery(this);
if ($select.hasClass('select2-hidden-accessible')) return;
$select.select2({
width: '100%',
minimumResultsForSearch: 6,
placeholder: $select.attr('placeholder') || 'Select option'
});
});
}
document.querySelectorAll('.sa-actions-inline a, .sa-actions-inline button').forEach((el) => {
const text = (el.textContent || '').trim() || 'Action';
if (!el.getAttribute('title')) el.setAttribute('title', text);
if (!el.getAttribute('aria-label')) el.setAttribute('aria-label', text);
});
});