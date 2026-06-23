document.addEventListener('DOMContentLoaded', function () {
    // Apply searchable select2 to all admin dropdowns if Select2 is loaded.
    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        jQuery('select').each(function () {
            const $select = jQuery(this);
            if ($select.hasClass('select2-hidden-accessible')) return;

            $select.select2({
                width: 'resolve',
                minimumResultsForSearch: 6,
                placeholder: $select.attr('placeholder') || 'Select option'
            });
        });
    }

    // Add tooltips/labels to compact icon action buttons.
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

    // Make admin tables easier to scan.
    document.querySelectorAll('.sa-table-wrap, .ka-table-wrap').forEach((wrap) => {
        wrap.setAttribute('tabindex', '0');
    });
});
