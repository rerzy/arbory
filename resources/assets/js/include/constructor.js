jQuery(document).ready(function () {
    jQuery('body').on('click', '.constructor-dialog .js-select-block', function (e) {
        e.preventDefault();

        const target = jQuery(e.target);

        const name = target.data('name');
        const field = target.data('field');

        const constructor = jQuery('body').find(`.type-constructor[data-namespaced-name="${field}"]`);

        var templates = constructor.data('templates');

        if(name in templates) {
            constructor.trigger('nestedfieldscreate', {
                target_block: constructor,
                template: jQuery(templates[name])
            });

            jQuery('body').trigger('ajaxboxclose');
        }
    })

    let body = $('body');
    let overview = $('.overview');
    let status = overview.find('.status');
    let form = overview.siblings('form');
    let modalOpener = overview.find('.overview-status-open, .overview-constructor-open');
    let confirmator = overview.find('.overview-status-confirm');
    let inputs = overview.find('.status > .fields input');
    let activateAt = status.find('input[name="resource\[activate_at\]"]');
    let expireAt = status.find('input[name="resource\[expire_at\]"]');

    inputs.attr('form', form.attr('id'));

    modalOpener.bind('click', function (e) {
        let opener = $(this);

        opener.trigger('ajaxboxopen', {
            content: opener.attr('href'),
            trigger: opener
        });
    });

    body.on('ajaxboxaftershow', function (event, mfp, data) {
        if ('trigger' in data) {
            if (data.trigger.is('.overview-status-open')) {
                let closer = $('.dialog.status .close');

                closer.on('click', resetActivationValues);
            }
        }
    });

    function resetActivationValues() {
        let closer = $(this);
        let modal = closer.parents('.dialog');

        modal.find('input[name="status\[activate_at\]"]').val(activateAt.val());
        modal.find('input[name="status\[expire_at\]"]').val(expireAt.val());
    }

    confirmator.bind('click', function (e) {
        let modal = confirmator.parents('.dialog');
        let checked = modal.find('input[name="status\[published_status\]"]:checked');
        let value = checked.length ? checked.val() : 'unpublished';

        status.find('[value=' + value + ']').prop('checked', true);
        status.find('.option').removeClass('selected');
        status.find('.option[data-published-value=' + value + ']').addClass('selected');

        let modalActivateAt = modal.find('input[name="status\[activate_at\]"]');
        let modalExpireAt = modal.find('input[name="status\[expire_at\]"]');

        activateAt.val(modalActivateAt.val());
        expireAt.val(modalExpireAt.val());

        body.trigger('ajaxboxclose');
    });
});