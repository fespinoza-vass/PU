define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'domReady!'
], function ($, modal, url) {
    'use strict';

    return {
        modalContent: '#minicart-proceed-confirmation-popup-content',
        available: null,

        /** Create modal */
        createModal: function () {
            var options = {
                'type': 'popup',
                'modalClass': 'minicart-proceed-confirmation',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout',
                'buttons': [
                    {
                        text: $.mage.__('Editar Carrito'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Go to Cart
                         */
                        click: function () {
                            this.closeModal();
                            console.log(window.checkout.shoppingCartUrl);
                            window.location.href = window.checkout.shoppingCartUrl;
                        }
                    },
                    {
                        text: $.mage.__('Continuar Compra'),
                        class: 'action-primary action-accept',

                        /**
                         * Go to Checkout
                         */
                        click: function () {
                            this.closeModal();
                            console.log(window.checkout.checkoutUrl);
                            window.location.href = window.checkout.checkoutUrl;
                        }
                    }
                ]
            };

            modal(options, this.modalContent);
            $(this.modalContent).modal('openModal');
        },

        /**
         * Validations
         * @param route
         */
        validations: function (route) {
            $.ajax({
                context: this,
                url: BASE_URL + '/bopis/ajax/fastdeliveryavailable', // La URL de tu controlador
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    if (response.available === '0') {
                        this.createModal();
                    } else {
                        if (route === 1) {
                            window.location.href = window.checkout.shoppingCartUrl;
                        } else {
                            window.location.href = window.checkout.checkoutUrl;
                        }
                    }
                    console.log('success:' + response.available);
                },
                error: function (response) {
                    console.log(response);
                }
            }).done(function (response) {
                console.log('respuesta:' + response.available);
            });
        },

        /** Show popup window */
        showModal: function (route) {
            this.validations(route);
        }
    };
});
