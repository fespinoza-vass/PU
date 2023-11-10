define([
    'ko',
    'underscore',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'WolfSellers_Checkout/js/model/step-summary',
    'domReady!'
], function (
     ko,
     _,
     $,
     Component,
     registry,
     $t,
     messageList,
     shippingPayment,
     customer,
     checkoutData,
     stepSummary
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/payment-continue',
            namePrefixPayment: 'checkout.steps.billing-step.payment.payments-list.before-place-order.payments-continue',
        },
        isVisible: ko.observable(true),
        isPaymentStepFinished : ko.observable(false),
        isPaymentFinished : ko.observable(false),
        switchText : ko.observable(true),

        /**
         * function initialialize
         * @return {*}
         */
        initialize: function () {
            this._super();
            this.isPaymentFinished.subscribe(function (value){
                if (!value){
                    shippingPayment.isStepTwoFinished('_complete');
                    shippingPayment.isPaymentStepFinished('_complete');
                    stepSummary.isStepTreeFinished('_active');
                }else{
                    shippingPayment.isStepTwoFinished('_active');
                    shippingPayment.isPaymentStepFinished('_active');
                    stepSummary.isStepTreeFinished('');
                }
                this.isPaymentFinished(value);
            }, this);

            return this;
        },

        /**
         * function init
         */
        initConfig: function () {
            this._super();
            return this;
        },

        /**
         * function get checked payment and change status
         */
        setPaymentInformationCustomer: function () {
            this.switchText(!this.switchText());
            var changeText = registry.get(this.namePrefixPayment);
            if (checkoutData.getSelectedPaymentMethod() == null) {
                messageList.addErrorMessage({message: 'No se seleccionó ningún método de pago.'});
                this.isPaymentFinished(true);
            } else {
                if (customer.isCustomerStepFinished() === '_complete' &&
                    shippingPayment.isShippingStepFinished() === '_complete' &&
                    changeText.switchText() == false
                   ) {
                    this.isPaymentFinished(false);
                    this.isPaymentFinished.notifySubscribers(false);
                } else {
                    this.isPaymentFinished(true);
                }
            }
        },
    });
});
