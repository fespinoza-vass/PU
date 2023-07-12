define([
    'ko'
],function (ko) {
    'use strict';
    var paymentMixin = {
        isVisible: ko.observable(true),
        initialize: function () {
            this.isVisible(true);
            this._super();
            this.isVisible.subscribe(function (value) {
                console.trace();
            },this);
            return this;
        }
    }

    return function(paymentTarget){
        return paymentTarget.extend(paymentMixin);
    }
});
