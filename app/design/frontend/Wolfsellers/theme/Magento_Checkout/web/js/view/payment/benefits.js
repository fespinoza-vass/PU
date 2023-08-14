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
                // title: 'Example Modal',
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: 'payment-benefits-modal',
                    click: function () {
                        this.closeModal();
                    }
                }]
            },
            /**
             * Function to open modal.
             */
            openBenefitsModal: function () {
                var popup = modal(this.options, $('#popup-benefits-wrapper'));
                $('#popup-benefits-wrapper').modal('openModal');
            },
            /**
             * Function to return image url for the modal.
             * @returns {string}
             */
            getBenefitsImageUrl: function () {
                return window.checkoutConfig.staticBaseUrl + '/#';
            },
            /**
             * Function to return Alt info for the image.
             * @returns {*}
             */
            getBenefitsImageAlt: function () {
                return $.mage.__('Payment Benefits');
            }
        });
    }
);
