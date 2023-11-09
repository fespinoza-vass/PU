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
            if(quote.billingAddress().extensionAttributes !== undefined && quote.billingAddress().extensionAttributes.pickup_location_code !== undefined){
                var pickup_location_code = quote.billingAddress().extensionAttributes.pickup_location_code;
            }
            quote.billingAddress().extensionAttributes = {};
            // trigger form validation
            this.source.set('params.invalid', false);
            this.source.trigger('customCheckoutForm.data.validate');
            // verify that form data is valid
            if (!this.source.get('params.invalid')) {
                var formData = this.source.get('customCheckoutForm');
                var shipping = quote.shippingAddress();
                var customAtributes = shipping.customAttributes;
                if(quote.shippingMethod().carrier_code.includes('instore')){
                    customAtributes = [
                        {
                            "attribute_code": "ruc",
                            "value": ""
                        },
                        {
                            "attribute_code": "razon_social",
                            "value": ""
                        },
                        {
                            "attribute_code": "direccion_fiscal",
                            "value": ""
                        },
                        {
                            "attribute_code": "invoice_required",
                            "value": false,
                            "label": "No"
                        }
                    ];
                }
                customAtributes.forEach( function(value, index, array) {
                    if(value['attribute_code'] === 'invoice_required'){
                        quote.billingAddress().extensionAttributes.invoice_required = 1;
                        value['value'] = 1;
                        value['label'] = 'Si';
                    }
                    if(value['attribute_code'] === 'razon_social'){
                        quote.billingAddress().extensionAttributes.razon_social = formData.razon_social;
                        value['value'] = formData.razon_social;
                    }
                    if(value['attribute_code'] === 'direccion_fiscal'){
                        quote.billingAddress().extensionAttributes.direccion_fiscal = formData.direccion_fiscal;
                        value['value'] = formData.direccion_fiscal;
                    }
                    if(value['attribute_code'] === 'ruc'){
                        quote.billingAddress().extensionAttributes.ruc = formData.ruc;
                        value['value'] = formData.ruc;
                    }
                });

                $('input[name="ruc"]').prop('disabled', 'disabled');
                $('input[name="razon_social"]').prop('disabled', 'disabled');
                $('input[name="direccion_fiscal"]').prop('disabled', 'disabled');
                $('#submitInvoice').hide();
                $('#editInvoice').show();

                if(quote.billingAddress().extensionAttributes !== undefined && quote.billingAddress().extensionAttributes.pickup_location_code !== undefined){
                    quote.billingAddress().extensionAttributes.pickup_location_code = pickup_location_code;
                }
                messageList.addSuccessMessage({ message: $.mage.__('Información de facturación guardada') });

            }
        }
    });
});
