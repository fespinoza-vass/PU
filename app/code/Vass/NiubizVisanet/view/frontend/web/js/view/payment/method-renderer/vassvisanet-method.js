define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Vault/js/view/payment/vault-enabler',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/customer-data',
    'payform'
],
function ($, Component, url, quote, VaultEnabler,fullScreenLoader,customerData,paylib
    ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Vass_NiubizVisanet/payment/vassvisanet',
            confPaymentUrl: url.build('vassvisanet/visa/hello'),
            additionalData: {},
            paymenToken: null
        },

        context: function() {
            return this;
        },

        getCode: function() {
            return 'vassvisanet';
        },

        isActive: function() {
            return true;
        },

        getData: function () {

            var data = {

                'method': this.getCode(),

                'additional_data': {

                    'payment_token': this.paymenToken

                }

            };


            return data;

        },

        /**
         * Place order
         */
         beforePlaceOrder: function () {
            this.getPaymentConf();
        },

        /**
             * Get full selector name
             *
             * @param {String} field
             * @returns {String}
             */
         getSelector: function (field) {
            return '#' + this.getCode() + '_' + field;
        },

        getCustomerDetails: function () {
            var billingAddress = quote.billingAddress();
            return {
                firstName: billingAddress.firstname,
                lastName: billingAddress.lastname,
                phone: billingAddress.telephone,
                email: typeof quote.guestEmail === 'string' ? quote.guestEmail : window.checkoutConfig.customerData.email
            }
        },

        sendPaymentVal: function(sessionToken,merchantId,totals) {

            var data = {
                name: this.getCustomerDetails().firstName,
                lastName: this.getCustomerDetails().lastname,
                email: this.getCustomerDetails().email
                };

            var configuration = {
                sessionkey: sessionToken,
                channel: 'web',
                merchantid: merchantId,
                purchasenumber: quote.getQuoteId(),
                amount: totals,
                language: 'es',
                font: 'https://fonts.googleapis.com/css?family=Montserrat:400&display=swap',
                };
                window.payform.setConfiguration(configuration);
                var cardNumber = this.getSelector('cc_number');
                var cardExpiry = this.getSelector('expirationDate');
                var cardCvc = this.getSelector('cc_cid');

                console.log(data);
                console.log(configuration);

                return cardNumber+cardExpiry+cardCvc;
                /* Caso de uso: Controles independientes */
                // window.payform.createToken([cardNumber,cardExpiry,cardCvc], data).then(function(response){
                // return response;
                //     }).catch(function(error){
                //         fullScreenLoader.stopLoader();
                //         globalMessageList.addErrorMessage({
                //             message: error.message
                //         });
                // })
        },

        /**
         * Send request to get payment method nonce
         */
         getPaymentConf: function () {
            var self = this;
            alert('aqui');

            fullScreenLoader.startLoader();
            $.getJSON(self.confPaymentUrl, {
                data: JSON.stringify({})
            })
                .done(function (response) {
                    fullScreenLoader.stopLoader();
                    let total = quote.getCalculatedTotal();

                    var payment = self.sendPaymentVal(response.token, response.merchant, total);
                    console.log(payment);

                    fullScreenLoader.stopLoader();

                    this.paymenToken = payment;
                    if(this.paymenToken !== null){
                        self.placeOrder();
                    }else{
                        fullScreenLoader.stopLoader();
                        globalMessageList.addErrorMessage({
                            message: 'Intente nuevamente'
                        });
                    }
                })
                .fail(function (response) {
                    var error = JSON.parse(response.responseText);

                    fullScreenLoader.stopLoader();
                    globalMessageList.addErrorMessage({
                        message: error.message
                    });
                });
        }
    });
}
);


