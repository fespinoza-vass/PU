define([
    'ko'
],function (
    ko
) {
    'use strict';
    var listMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/payment-methods/list',
        },
        /**
         * Set title for payment list group
         * @returns {string}
         */
        getGroupTitle: function () {
            return "2.Elige un método de pago";
        }
    };

    return function (listTarget) {
        return listTarget.extend(listMixin);
    }
})
