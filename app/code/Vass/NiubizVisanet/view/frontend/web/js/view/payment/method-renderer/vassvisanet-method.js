define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form'
],
function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Vass_NiubizVisanet/payment/vassvisanet'
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'vassvisanet';
        },

        isActive: function() {
            return true;
        }
    });
}
);