define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Izipay_Core/js/model/validatePhonenumber'
    ],
    function (Component, additionalValidators, validatePhonenumber) {
        'use strict';
        additionalValidators.registerValidator(validatePhonenumber);
        return Component.extend({});
    }
);