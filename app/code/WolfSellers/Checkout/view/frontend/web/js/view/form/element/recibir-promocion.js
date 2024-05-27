define([
    'ko',
    'uiComponent',
    'jquery',
    'WolfSellers_Checkout/js/view/form/element/promotional-agreements-popup',
    'mage/url',
    'domReady!'
], function (ko, Component, $, promotionalAgreements, urlBuilder) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/form/element/recibir-promocion'
        },

        isSubscribed: ko.observable(false),

        initialize: function () {
            var self = this;
            this._super();
        },

        /**
         * Create Modal
         */
        createInformationModal: function () {
            try {
                if (promotionalAgreements.modalContent == null) {
                    promotionalAgreements.createModal('#promotional-agreements-modal');
                }
            }catch (e) {
                console.log(e.toString());
            }
        },
        /**
         * Show Modal
         */
        showModal: function () {
            this.createInformationModal();
            promotionalAgreements.showModal();
        }
    });
});




