define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'domReady!'
    ],
    function($, Component, modal) {
        'use strict';
        // return component modal
        return Component.extend({
            // modal configurations
            options: {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'container-payment-benefits-modal',
                buttons: []
            },
            /**
             * Function to open modal.
             */
            openBenefitsModal: function () {
                var popup = modal(this.options, $('#popup-benefits-wrapper'));
                $('#popup-benefits-wrapper').modal('openModal');
            },
            /**
             * Function to return Alt info for the image.
             * @returns {*}
             */
            getBenefitsImageAlt: function () {
                return $.mage.__('Know our benefits with cards');
            }
        });
    }
);
