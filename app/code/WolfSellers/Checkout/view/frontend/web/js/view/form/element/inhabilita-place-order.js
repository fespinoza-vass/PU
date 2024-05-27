define([
    'jquery',
    'domReady!'
], function($) {
    'use strict';

    $(function(){
        $(document).on('change', 'input[name*="agreement"]', function(event) {
            var selection = event.currentTarget;

            if (selection.checked) {
                $('.action.primary.checkout.amasty').removeAttr('disabled');
            } else {
                $('.action.primary.checkout.amasty').prop('disabled', 'disabled');
            }
        })
    });
});
