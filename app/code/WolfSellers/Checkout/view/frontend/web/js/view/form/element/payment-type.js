define([
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'Magento_Checkout/js/model/quote',
], function(ko, registry, Abstract, $, quote) {
    'use strict';

    return Abstract.extend({
        defaults: {
            invoiceFormTemplate: 'WolfSellers_Checkout/payment-data/invoice-form'
        },
        initialize: function () {
            this._super();
            registry.get('index = ruc').hide();
            registry.get('index = razon_social').hide();
            registry.get('index = direccion_fiscal').hide();
            return this;
        },
        /**
         * Detect changes type_payment value
         */
        click: function(data, event) {
            this.change(event.target.value);
            return true;
        },
        /**
         * If invoice require change update billing address data 
         */
        change: function(value) {
            var billing = quote.billingAddress();
            var customAtributes = billing.customAttributes;
            if (value === 'boleta') {
                customAtributes.forEach( function(value, index, array) {
                    if (value['attribute_code'] === 'invoice_required') {
                        value['value'] = 0;
                    }
                });
                registry.get('index = ruc').hide();
                registry.get('index = razon_social').hide();
                registry.get('index = direccion_fiscal').hide();
                $('#editInvoice').hide();
                $('#submitInvoice').hide();
            } else if (value === 'factura') {
                customAtributes.forEach( function(value, index, array) {
                    if (value['attribute_code'] === 'invoice_required') {
                        value['value'] = 1;
                    }
                });
                registry.get('index = ruc').show();
                registry.get('index = razon_social').show();
                registry.get('index = direccion_fiscal').show();

                if($('input[name="ruc"]').attr('disabled')=="disabled"){
                    $('#submitInvoice').hide();
                    $('#editInvoice').show();
                }else{
                    $('#submitInvoice').show();
                }
            }
        }
    });
});
