define([
    'ko',
    'uiComponent',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer'
],function (
    ko,
    Component,
    shippingPayment,
    customer
) {
    'use strict';
    return Component.extend({
        isVisibleShipping:ko.observable(true),
        isFirstStepFinished:ko.observable(false),
        /**
         * function initialize
         */
        initialize: function () {
            this._super();
            this.isVisibleShipping.subscribe(function (value) {
                if (!value){
                    shippingPayment.isShippingStepFinished('_complete');
                }else{
                    shippingPayment.isShippingStepFinished('_active');
                }
            }, this);
            customer.isCustomerStepFinished.subscribe(function (value) {
                this.isFirstStepFinished(false);
                if(value.includes('_complete')){
                    this.isFirstStepFinished(true);
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
