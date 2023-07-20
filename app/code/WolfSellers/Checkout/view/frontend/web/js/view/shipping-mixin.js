define([
    'jquery',
    'ko',
],function (
    $,
    ko
) {
    'use strict';
    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
        },
        initialize: function () {
            this._super();
            return this;
        }
    }


    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }



});
