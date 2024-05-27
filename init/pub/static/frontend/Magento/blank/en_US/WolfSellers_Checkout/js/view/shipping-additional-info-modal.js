define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'domReady!'
], function ($, modal, url) {
    'use strict';

    return {
        modalContentForRegularDelivery: null,
        modalContentForFastDelivery: null,
        /**
         * Create Modals
         * @param element
         */
        createModalRS: function (element) {
            var options = {
                'type': 'popup',
                'modalClass': 'regular-additional-info-modal',
                'responsive': true,
                'innerScroll': true
            };

            this.modalContentForRegularDelivery = element;
            modal(options, this.modalContentForRegularDelivery);
        },
        /**
         * Create Modals
         * @param element
         */
        createModalFS: function (element) {
            var options = {
                'type': 'popup',
                'modalClass': 'fast-additional-info-modal',
                'responsive': true,
                'innerScroll': true
            };

            this.modalContentForFastDelivery = element;
            modal(options, this.modalContentForFastDelivery);
        },
        /**
         * Show Additional Modal Information for Regular Shipping
         */
        showModalRSInformation: function () {
            console.log(this.modalContentForRegularDelivery);
            $(this.modalContentForRegularDelivery).modal('openModal');
        },
        /**
         * Show Additional Modal Information for Fast Shipping
         */
        showModalFSInformation: function () {
            console.log(this.modalContentForFastDelivery);
            $(this.modalContentForFastDelivery).modal('openModal');
        }
    };
});
