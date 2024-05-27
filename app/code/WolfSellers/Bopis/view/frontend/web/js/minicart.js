define([
    'jquery',
    'WolfSellers_Bopis/js/minicart/proceed-confirmation-popup',
    'Magento_Customer/js/customer-data'
], function ($, proceedPopup, customerData) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * Create Modal
             */
            setModalElement: function () {
                if (proceedPopup.modalContent == null) {
                    proceedPopup.createModal('#minicart-proceed-confirmation-popup-content');
                }
            },

            /**
             * @override
             */
            getCartParam: function (name) {
                if (name === 'possible_onepage_checkout') {
                    $(document).on('click', '.checkout.minicart-submit,.action.viewcart', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var route = 0;
                        if ($(this).hasClass('viewcart')) {
                            $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
                            $('body').removeClass('cart-open');
                            route = 1;
                        }

                        proceedPopup.validations(route);
                    });

                }
                return this._super(name);
            },

            /**
             * Stop event propagation and run validations.
             */
            stopPropagationAndValidation: function (event) {
                event.stopPropagation();
                event.preventDefault();

                var route = 0;
                if ($(this).hasClass('viewcart')) {
                    $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
                    $('body').removeClass('cart-open');
                    route = 1;
                }

                proceedPopup.validations(route);
            }
        });
    }
});
