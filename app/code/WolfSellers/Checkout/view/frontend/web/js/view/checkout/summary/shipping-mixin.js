define([
    'ko'
], function (
    ko
) {
    'use strict';
    var shippingMixin = {
        defaults: {
            template: 'WolfSellers_Checkout/checkout/summary/shipping'
        },
    };
    return function (shippingTarget) {
        return shippingTarget.extend(shippingMixin);
    }
})
