define([
    'ko',
    'uiComponent',
    'jquery'
], function (ko, Component,$) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/form/element/recibir-promocion'
        },

        isSubscribed: ko.observable(false),

        initialize: function () {
            var self = this;
            this._super();
        }
    });
});




