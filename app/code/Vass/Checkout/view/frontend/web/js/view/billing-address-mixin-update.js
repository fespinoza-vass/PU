/**
 * Billing address view mixin for store flag is billing form in edit mode (visible)
 */
define([
    'ko',
    'underscore',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function (ko, _, customer, quote, checkoutData) {
    'use strict';
    return function (updateAddress) {

        return wrapper.wrap(updateAddress, function (originalUpdateAddress, config, element) {

            originalUpdateAddress(config, element);

            console.log('aqui');

        });

    };
});
