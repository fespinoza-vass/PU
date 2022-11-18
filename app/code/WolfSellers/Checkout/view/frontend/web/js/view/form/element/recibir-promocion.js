define([
    'ko',
    'uiComponent',
    'jquery',
    'domReady!'
], function (ko, Component,$) {

    "use strict";

    return Component.extend({

        defaults: {
            template: 'WolfSellers_Checkout/form/element/recibir-promocion'
        },
    
        isSubscribed: false,

        initialize: function () {
            var self = this;
            this._super();
        }

    });
});




