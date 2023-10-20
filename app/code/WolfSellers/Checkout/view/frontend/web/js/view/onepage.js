define([
    'ko',
    'uiComponent',
    'WolfSellers_Checkout/js/model/shipping-payment',
],function (
    ko,
    Component,
    shippingPayment
) {
    'use strict';
    return Component.extend({
        isVisibleShipping:ko.observable(true),
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
