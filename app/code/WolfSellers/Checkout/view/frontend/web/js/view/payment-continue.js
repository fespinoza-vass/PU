define([
    'ko',
    'underscore',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'WolfSellers_Checkout/js/model/step-summary',
    'domReady!'
], function (
     ko,
     _,
     $,
     Component,
     registry,
     $t,
     messageList,
     shippingPayment,
     customer,
     checkoutData,
     stepSummary
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/payment-continue',
            namePrefixPayment: 'checkout.steps.billing-step.payment.payments-list.before-place-order.payments-continue',
        },
        isVisible: ko.observable(true),
        isPaymentStepFinished : ko.observable(false),
        isPaymentFinished : ko.observable(false),
        switchText : ko.observable(true),

        /**
         * function initialialize
         * @return {*}
         */
        initialize: function () {
            this._super();
            this.isPaymentFinished.subscribe(function (value){
                if (!value){
                    shippingPayment.isStepTwoFinished('_complete');
                    shippingPayment.isPaymentStepFinished('_complete');
                    stepSummary.isStepTreeFinished('_active');
                }else{
                    shippingPayment.isStepTwoFinished('_active');
                    shippingPayment.isPaymentStepFinished('_active');
                    stepSummary.isStepTreeFinished('');
                }
                this.isPaymentFinished(value);
            }, this);

            return this;
        },

        /**
         * function init
         */
        initConfig: function () {
            this._super();
            return this;
        },

        /**
         * function get checked payment and change status
         */
        setPaymentInformationCustomer: function () {
            const paymentType = document.querySelector('.payment-type');

            function getSelectedPaymentType() {
                const selectedOption = paymentType.querySelector('input[name="payment_type"]:checked');
                return selectedOption ? selectedOption.value : null;
            }
        
            function updateReceipt() {
                const selectedPaymentType = getSelectedPaymentType();
        
                var selectReceipt = `
                    <p><span class="list-billing">Comprobante:</span> ${selectedPaymentType}</p>
                `;
                document.getElementById('selectReceipt').innerHTML = selectReceipt;
        
                var inputs = document.querySelectorAll('.input-text');
                var valores = {};
                inputs.forEach(function(input) {
                    var nombre = input.getAttribute('name');
                    var valor = input.value;
                    valores[nombre] = valor;
                });
        
                var detailReceipt = `
                    <p><span class="list-billing">Razón Social:</span> ${valores['razon_social']}</p>
                    <p><span class="list-billing">RUC:</span> ${valores['ruc']}</p>
                    <p><span class="list-billing">Dirección Fiscal:</span> ${valores['direccion_fiscal']}</p>
                `;
        
                if (selectedPaymentType && selectedPaymentType.toLowerCase() === "factura") {
                    document.getElementById('detailReceipt').innerHTML = detailReceipt;
                } else {
                    document.getElementById('detailReceipt').innerHTML = '';
                }
            }
        
            paymentType.addEventListener('change', updateReceipt);
            updateReceipt();
            
            const editBilling = document.getElementById('editBilling');
            const selectBilling = document.getElementById('checkout-payment-method-load');
            const customCheckoutForm = document.getElementById('custom-checkout-form');
            const resumeBilling = document.getElementById('resume-billing');

            editBilling.style.display = 'flex';
            selectBilling.style.display = 'none';
            customCheckoutForm.style.display = 'none';
            resumeBilling.style.display = 'flex';

            this.switchText(!this.switchText());
            var changeText = registry.get(this.namePrefixPayment);
            if (checkoutData.getSelectedPaymentMethod() == null) {
                messageList.addErrorMessage({message: 'No se seleccionó ningún método de pago.'});
                this.isPaymentFinished(true);
            } else {
                if (customer.isCustomerStepFinished() === '_complete' &&
                    shippingPayment.isShippingStepFinished() === '_complete' &&
                    changeText.switchText() == false
                ) {
                    this.isPaymentFinished(false);
                    this.isPaymentFinished.notifySubscribers(false);
                } else {
                    this.isPaymentFinished(true);
                }
                $('html, body').animate({
                    scrollTop: ($("#opc-sidebar").offset().top - 50)
                }, 1000);
            }

            document.getElementById('opc-sidebar').scrollIntoView({ behavior: 'smooth' });
        },
    });
});

