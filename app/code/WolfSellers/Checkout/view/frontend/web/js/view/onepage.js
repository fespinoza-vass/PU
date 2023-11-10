define([
    'ko',
    'uiComponent',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'uiRegistry'
],function (
    ko,
    Component,
    shippingPayment,
    customer,
    registry
) {
    'use strict';
    return Component.extend({
        isVisibleShipping:ko.observable(true),
        isFirstStepFinished:ko.observable(true),
        /**
         * function initialize
         */
        initialize: function () {
            this._super();
            this.isVisibleShipping.subscribe(function (value) {
                if (!value){
                    shippingPayment.isShippingStepFinished('_complete');
                    var visanet = registry.get("checkout.steps.billing-step.payment.payments-list.visanet_pay");
                    visanet.selectPaymentMethod();
                }else{
                    shippingPayment.isShippingStepFinished('_active');
                }
            }, this);
            customer.isCustomerStepFinished.subscribe(function (value) {
                this.isFirstStepFinished(true);
                if(value.includes('_complete')){
                    this.isFirstStepFinished(false);
                }
            }, this);
            return this;
        },
        /**
         * Listen edit event when shipping summary its finished
         * @param parent
         */
        showShippingStep: function (parent) {
            parent.isVisibleShipping(true);
        }

    });
})
