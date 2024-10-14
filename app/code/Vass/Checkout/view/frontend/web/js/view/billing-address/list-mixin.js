define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'uiRegistry',
    'domReady!'
], function (ko, $, quote, $t, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                isNewAddressSelected: false,
            },

            /**
             * @param {Object} address
             */
            onAddressChange: function (address) {
                this.isNewAddressSelected(true);
            }
        });
    }
});
