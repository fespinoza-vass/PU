define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/validation-checkout'
],
    function (Component,additionalValidators,validationCheckout){
    'use strict';
        additionalValidators.registerValidator(validationCheckout);
        return Component.extend({});
    })
