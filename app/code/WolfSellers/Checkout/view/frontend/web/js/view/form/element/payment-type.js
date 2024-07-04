define([
    'ko',
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment'
], function(ko, registry,_, Abstract, $, quote,shippingPayment) {
    'use strict';

    return Abstract.extend({
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
            var shipping = quote.shippingAddress();
            var customAtributes = shipping.customAttributes;
            var isShippingStepFinished = _.isUndefined(shippingPayment.isShippingStepFinished()) ?
                    "" : shippingPayment.isShippingStepFinished();
            if(!isShippingStepFinished.includes('_complete')){
                return;
            }

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

            if (value === 'boleta') {
                customAtributes.forEach( function(value, index, array) {
                    if (value['attribute_code'] === 'invoice_required') {
                        value['value'] = 0;
                    }
                });

                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.ruc"
                ).hide();
                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.razon_social"
                ).hide();
                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.direccion_fiscal"
                ).hide();
                $('#editInvoice').hide();
                $('#submitInvoice').hide();
                $('.continuePaymentPu').prop('disabled',false);

            } else if (value === 'factura') {
                customAtributes.forEach( function(value, index, array) {
                    if (value['attribute_code'] === 'invoice_required') {
                        value['value'] = 1;
                    }
                });
                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.ruc"
                ).show();
                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.razon_social"
                ).show();
                registry.get("checkout.steps.billing-step.payment.beforeMethods.invoice-form.custom-checkout-form-fieldset.direccion_fiscal"
                ).show();

                if($('input[name="ruc"]').attr('disabled')==="disabled"){
                    $('#submitInvoice').hide();
                    $('#editInvoice').show();
                }else{
                    $('#submitInvoice').show();
                }

                $('.continuePaymentPu').prop('disabled',true);
            }
        }
    });
});
