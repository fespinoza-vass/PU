define([
    'ko',
    'underscore'
],function (
    ko,
    _
) {
    'use strict';

    return {
        isStepTreeFinished: ko.observable("_active"),
        isPlaceOrderFinished: ko.observable(""),
        isStepPlaceOrder : ko.observable("")
    }
})
