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
            template:'WolfSellers_Checkout/payment',
            links: {
                "IsDisablePaymentMethods":"checkout.steps.shipping-step.shippingAddress:goToResume",
                "IsDisablePaymentMethods":"checkout.steps.store-pickup.store-selector:goToResume"
            }
        },
        isVisible: ko.observable(true),
        isActive: ko.observable(false),
        IsDisabledPaymentStep : ko.observable(true),
        IsDisablePaymentMethods : ko.observable(true),
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

            this.IsDisabledPaymentStep(true);
            this.setIsDisabledPaymentStep();

            this.IsDisablePaymentMethods.subscribe(function(value){
                if(value === true){
                   return true;
                }else{
                    return false;
                }
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

        /**
         * function is visible payment when is shipping is complete
         */
        setIsDisabledPaymentStep: function () {
            if (shippingPayment.isShippingStepFinished() === '_complete'){
                this.IsDisabledPaymentStep(false);
            }else{
                this.IsDisabledPaymentStep(true);
            }
        },
    }

    return function(paymentTarget){
        return paymentTarget.extend(paymentMixin);
    }
});
