define([
    'ko',
    'Magento_Checkout/js/action/get-payment-information'
],function (
    ko,
    getPaymentInformation
) {
    'use strict';
    var paymentMixin = {
        isVisible: ko.observable(true),
        initialize: function () {
            var self = this;
            this._super();
            getPaymentInformation().done(function () {
                self.isVisible(true);
            });
            return this;
        },
        /**
         * Navigate method.
         */
        navigate: function () {
            var self = this;
            getPaymentInformation().done(function () {
                self.isVisible(true);
            });
        },
    }

    return function(paymentTarget){
        return paymentTarget.extend(paymentMixin);
    }
});
