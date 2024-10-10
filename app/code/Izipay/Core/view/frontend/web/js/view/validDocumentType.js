define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Izipay_Core/js/model/validDocumentType'
    ],
    function (Component, additionalValidators, validDocumentType) {
        'use strict';
        additionalValidators.registerValidator(validDocumentType);
        return Component.extend({});
    }
);