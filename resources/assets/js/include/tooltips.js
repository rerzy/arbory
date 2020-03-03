import tippy from 'tippy.js'

jQuery(() => {

    jQuery('body').on('tooltipsinit', e => {
        tippy('[data-tippy-content]');
    });

    jQuery('body').on('contentloaded', e => {
        jQuery(e.target).trigger('tooltipsinit');
    });
});