define([
    'ko',
    'underscore',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Magento_Ui/js/modal/alert'

], function ( ko, _, $, Component, registry, quote, $t, alert) {
    'use strict';
        return Component.extend({
            defaults: {
                template: 'WolfSellers_Checkout/button-place-order',
                paymentsNamePrefix: 'checkout.steps.billing-step.payment.payments-list.'
            },

            initialize: function () {
                self = this;
                this._super();
            },

            /**
             * function place order
             * @param data
             * @param event
             * @return {boolean}
             */
            placeOrder: function (data, event) {
                var  shippingComponent = registry.get(this.shippingFormPrefix);
                var  paymentMethod =   quote.paymentMethod();
                var  paymentComponentName = this.paymentsNamePrefix + paymentMethod.method;
                var  paymentComponent = registry.get(paymentComponentName);

               if(!_.isUndefined(paymentComponent)){
                   paymentComponent.placeOrder(paymentComponent,event);
               }
            }
        });
});
