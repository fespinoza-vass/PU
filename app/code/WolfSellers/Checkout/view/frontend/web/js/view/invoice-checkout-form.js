/*global define*/
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function($, ko, Component, quote, registry, messageList) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/invoice-data/invoice-checkout-form',
        },
        initialize: function () {
            this._super();
            return this;
        },
        /**
         * Edit data invoice 
         * Enable input to change billing address data 
         */
        editInfo: function() {
      
            $('input[name="ruc"]').removeAttr('disabled');
            $('input[name="razon_social"]').removeAttr('disabled');
            $('input[name="direccion_fiscal"]').removeAttr('disabled');
            $('#editInvoice').hide();
            $('#submitInvoice').show();
        },
        /**
         * Form submit handler
         * If invoice require, change billing address data 
         */
        onSubmit: function() {
            // trigger form validation
            this.source.set('params.invalid', false);
            this.source.trigger('customCheckoutForm.data.validate');
            // verify that form data is valid
            if (!this.source.get('params.invalid')) {
                var formData = this.source.get('customCheckoutForm');
                var billing = quote.billingAddress();
                var customAtributes = billing.customAttributes;

                customAtributes.forEach( function(value, index, array) {
                    if(value['attribute_code'] === 'invoice_required'){
                        value['value'] = 1;
                        value['label'] = 'Si';
                    }
                    if(value['attribute_code'] === 'razon_social'){
                        value['value'] = formData.razon_social;
                    }
                    if(value['attribute_code'] === 'direccion_fiscal'){
                        value['value'] = formData.direccion_fiscal;
                    }
                    if(value['attribute_code'] === 'ruc'){
                        value['value'] = formData.ruc;
                    }
                });

                $('input[name="ruc"]').prop('disabled', 'disabled');
                $('input[name="razon_social"]').prop('disabled', 'disabled');
                $('input[name="direccion_fiscal"]').prop('disabled', 'disabled');
                $('#submitInvoice').hide();
                $('#editInvoice').show();

                console.log(quote.billingAddress());
                messageList.addSuccessMessage({ message: $.mage.__('Información de facturación gurdada') });

            }
        }
    });
});
