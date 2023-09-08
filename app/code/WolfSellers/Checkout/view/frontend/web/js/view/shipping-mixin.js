define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'Magento_Checkout/js/model/quote'
],function (
    $,
    ko,
    stepNavigator,
    $t,
    quote
) {
    'use strict';
    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
        },
        isActive: ko.observable(false),

        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            return this;
        }
    }


    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }



});
