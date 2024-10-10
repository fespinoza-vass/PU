define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Amasty_CheckoutCore/js/model/payment/place-order-state',
    'domReady!'
], function (ko, $, quote, placeOrderState) {
    'use strict';

    return function (Component) {
        return Component.extend({
            isPlaceOrderActionAllowed: ko.pureComputed({

                read: function () {
                    let paymentMethod = quote.paymentMethod()?.method;
                    if (paymentMethod !== 'undefined' && paymentMethod === 'izipay') {
                        return placeOrderState();
                    }

                    return quote.billingAddress() !== null && placeOrderState();
                },
                write: function (value) {
                    return value;
                },
                owner: this
            })
        });
    }
});
