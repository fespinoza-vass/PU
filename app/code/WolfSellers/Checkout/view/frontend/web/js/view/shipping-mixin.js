define([
    'jquery',
    'ko',
],function (
    $,
    ko
) {
    'use strict';
    var shippingMixin = {
        initialize: function () {
            this._super();
            return this;
        }
    }


    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }



});
