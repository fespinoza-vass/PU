define([
    'ko',
    'underscore',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'WolfSellers_Checkout/js/model/step-summary',
    'Magento_Checkout/js/checkout-data',
], function (
     ko,
     _,
     $,
     Component,
     registry,
     quote,
     $t,
     messageList,
     shippingPayment,
     customer,
     stepSummary,
     checkoutData
) {
    'use strict';
        return Component.extend({
            defaults: {
                template: 'WolfSellers_Checkout/button-place-order',
                paymentsNamePrefix: 'checkout.steps.billing-step.payment.payments-list.'
            },
            isStepTreeFinished: ko.observable(""),
            isPlaceOrderFinished: ko.observable(""),
            isStepPlaceOrder : ko.observable(false),

            /**
             * change status progress bar
             * @return {*}
             */
            initialize: function () {
                this._super();
                this.isStepPlaceOrder.subscribe(function (value){
                    if (!value){
                        stepSummary.isPlaceOrderFinished('_complete');
                    }else{
                        stepSummary.isPlaceOrderFinished('_active');
                    }
                    this.isStepPlaceOrder(value);
                }, this);
                return this;
            },

            /**
             * function place order and change complete
             * @param data
             * @param event
             * @return {boolean}
             */
            placeOrder: function (data, event) {
                if (customer.isCustomerStepFinished() === '_complete' &&
                    shippingPayment.isShippingStepFinished() === '_complete' &&
                    shippingPayment.isPaymentStepFinished() === '_complete') {
                    messageList.addErrorMessage({message: 'Metodo de Pago completado.'});
                    var shippingComponent = registry.get(this.shippingFormPrefix);
                    var paymentMethod = quote.paymentMethod();
                    var paymentComponentName = this.paymentsNamePrefix + paymentMethod.method;
                    var paymentComponent = registry.get(paymentComponentName);


                    if (!_.isUndefined(paymentComponent)) {
                        paymentComponent.placeOrder(paymentComponent, event);
                    }
                }else {
                    messageList.addErrorMessage({message: 'Complete la informacion solicitada.'});
                    this.isStepPlaceOrder(true);
                }
            }
        });
});
