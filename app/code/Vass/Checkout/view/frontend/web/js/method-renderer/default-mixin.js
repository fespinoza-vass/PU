define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * Place order.
             */
            placeOrder: function (data, event) {
                let checkboxPrivacy = $('[name="sidebar[additional][checkbox_privacidad]');
                if (!checkboxPrivacy.is(':checked')) {
                    let message = $t('This is a required field.');
                    let id = checkboxPrivacy.attr('id');
                    checkboxPrivacy.siblings('.field-error').remove();
                    checkboxPrivacy.siblings('label')
                        .after(`<div class="field-error" id="error-${id}">${message}</div>`);
                    event.preventDefault();
                }

                this._super();
            }
        });
    }
});
