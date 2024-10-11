define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Izipay_Core/js/model/hasAlternativePaymentIzipay'
    ],
    function (Component, additionalValidators, alternativePaymentIzipayValidation) {
        'use strict';
        additionalValidators.registerValidator(alternativePaymentIzipayValidation);
        return Component.extend({});
    }
);