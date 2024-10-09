require([
    'jquery',
    'uiComponent',
    'domReady!'
], function($, Component) {
    'use strict';

    $(document).ready(function () {
        $(document).on('click', '.quantity-button', function (event) {
            if ($('#izipay-iframe-payment').children().length > 0) {
                return;
            }

            let input = $(this).parent().find('.qty');
            let actualQty = parseInt(input.val());

            if (isNaN(actualQty)) {
                actualQty = 1;
            }

            if ($(this).hasClass('increase')) {
                input.val(actualQty + 1);
            } else if ($(this).hasClass('decrease')) {
                if (actualQty > 1) input.val(actualQty - 1)
                else event.preventDefault();
            }
        });
    });
});
