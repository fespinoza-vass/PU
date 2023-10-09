define([
    'ko',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate'
],function (
    ko,
    getPaymentInformation,
    stepNavigator,
    $t
) {
    'use strict';
    var paymentMixin = {
        defaults:{
            template:'WolfSellers_Checkout/payment'
        },
        isVisible: ko.observable(true),
        isActive: ko.observable(false),

        initialize: function () {
            var self = this;
            this._super();
            var modifyData= {
                title : $t('Resumen de pago')
            };

            stepNavigator.modifyStep("payment", modifyData);
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
