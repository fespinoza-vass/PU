define([
    'ko'
],function (
    ko
) {
    'use strict';

    return {
        isShippingStepFinished: ko.observable(),
        isPaymentStepFinished: ko.observable(),
        isStepTwoFinished: ko.observable('_active')
    }
})
