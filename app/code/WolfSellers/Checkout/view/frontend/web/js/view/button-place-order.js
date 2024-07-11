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
            paymentsNamePrefix: 'checkout.steps.billing-step.payment.payments-list.',
        },
        isStepTreeFinished: ko.observable(""),
        isPlaceOrderFinished: ko.observable(""),
        isStepPlaceOrder: ko.observable(false),
        isPlaceOrderDisabled: ko.observable(),
        isPlaceOrderInitialized: ko.observable(false),

        /**
         * Inicialización del componente
         * @return {*}
         */
        initialize: function () {
            this._super();

            // Suscripción al observable isStepPlaceOrder para actualizar isStepTreeFinished
            this.isStepPlaceOrder.subscribe(function (value) {
                if (!value) {
                    stepSummary.isStepTreeFinished('_complete');
                } else {
                    stepSummary.isStepTreeFinished('_active');
                }
                this.isStepPlaceOrder(value);
            }, this);

            // Computed observable para controlar si el botón Place Order está deshabilitado
            this.isPlaceOrderDisabled = ko.computed(function () {
                if (this.isPlaceOrderInitialized()) {
                    return false;
                }
                const isDisabled = !(customer.isCustomerStepFinished() === '_complete' &&
                    shippingPayment.isShippingStepFinished() === '_complete' &&
                    shippingPayment.isPaymentStepFinished() === '_complete');
                if (!isDisabled) {
                    this.isPlaceOrderInitialized(true);
                }
                return isDisabled;
            }, this);

            return this;
        },

        /**
         * Función para realizar el pedido
         * @param data
         * @param event
         * @return {boolean}
         */
        placeOrder: function (data, event) {
            if (customer.isCustomerStepFinished() === '_complete' &&
                shippingPayment.isShippingStepFinished() === '_complete' &&
                shippingPayment.isPaymentStepFinished() === '_complete') {

                var shippingComponent = registry.get(this.shippingFormPrefix);
                var paymentMethod = quote.paymentMethod();
                var paymentComponentName = this.paymentsNamePrefix + paymentMethod.method;
                var paymentComponent = registry.get(paymentComponentName);

                if (!_.isUndefined(paymentComponent)) {
                    if (paymentMethod.method === 'visanet_pay') {
                        document.getElementById('placeOrder').disabled = true;
                        paymentComponent.loadCheckoutJS();
                    } else {
                        paymentComponent.placeOrder(paymentComponent, event);
                    }
                }
            } else {
                messageList.addErrorMessage({message: 'Complete la informacion solicitada.'});
                this.isStepPlaceOrder(true);
            }
        }
    });
});