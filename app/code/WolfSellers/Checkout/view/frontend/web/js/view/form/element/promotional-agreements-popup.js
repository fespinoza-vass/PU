define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'domReady!'
], function ($, modal, url) {
    'use strict';

    return {
        modalContent: null,
        /**
         * Create Modals
         * @param element
         */
        createModal: function (element) {
            var options = {
                'type': 'popup',
                'modalClass': 'promotional-agreements-modal',
                'responsive': true,
                'innerScroll': true,
                'buttons': [
                    {
                        text: $.mage.__('Cerrar'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Close Modal
                         */
                        click: function () {
                            this.closeModal();
                        }
                    }
                ]
            };

            this.modalContent = element;
            modal(options, this.modalContent);
        },
        /**
         * Show Modal Information
         */
        showModal: function () {
            $(this.modalContent).modal('openModal');
        },
    };
});
