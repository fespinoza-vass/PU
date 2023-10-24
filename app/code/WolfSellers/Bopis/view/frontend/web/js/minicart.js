define([
    'jquery',
    'WolfSellers_Bopis/js/minicart/proceed-confirmation-popup',
    'Magento_Customer/js/customer-data'
], function ($, proceedPopup, customerData
) {
    'use strict';

    return function (Component) {
        return Component.extend({

            /**
             * @override
             */
            getCartParam: function (name) {

                if (name === 'possible_onepage_checkout') {
                    $('#top-cart-btn-checkout,.action.viewcart').click(function (event) {
                        event.preventDefault();

                        var route = 0;
                        if ($(this).hasClass('viewcart')) {
                            $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
                            $('body').removeClass('cart-open');
                            route = 1;
                        }

                        proceedPopup.showModal(route);
                        return false;
                    });

                }
                return this._super(name);
            },
        });
    }
});
