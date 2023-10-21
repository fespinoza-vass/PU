define([
    'ko',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'WolfSellers_Checkout/js/model/shipping-payment',
],function (
    ko,
    getPaymentInformation,
    stepNavigator,
    $t,
    shippingPayment
) {
    'use strict';
    var paymentMixin = {
        defaults:{
            template:'WolfSellers_Checkout/payment'
        },
        isVisible: ko.observable(true),
        isActive: ko.observable(false),

        /**
         * initialize
         * @return {paymentMixin}
         */
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

            this.setIsDisabledPaymentStep();
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

        /**
         * function is visible payment when is shipping is complete
         */
        setIsDisabledPaymentStep: function () {
            if (shippingPayment.isShippingStepFinished() === '_complete'){
                this.isVisible(true);
            }else{
                this.isVisible(false);
            }
        },
    }

    return function(paymentTarget){
        return paymentTarget.extend(paymentMixin);
    }
});
