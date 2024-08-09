define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Vault/js/view/payment/vault-enabler',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/customer-data',
    'payform',
    'ko'
],
function ($, Component, url, quote, VaultEnabler,fullScreenLoader,customerData,paylib,ko
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

        createCard: async function(){
            try {
                const response = await payform.createElement(
                  'card-number', {
                    style: this.elementStyles,
                    placeholder: 'Nro Tarjeta'
                  }, 'txtNumeroTarjeta'
                );
                console.log('Resultado Nro Tarjeta:', response);
              } catch (error) {
                console.error('Error al crear el elemento:', error);
              }
        },
        createExpiry: async function(){
            try {
                const response = await payform.createElement(
                  'card-expiry', {
                    style: this.elementStyles,
                    placeholder: 'Expiry'
                  }, 'txtFechaVencimiento'
                );
                console.log('Resultado Expiry:', response);
              } catch (error) {
                console.error('Error al crear el elemento:', error);
              }
        },
        createCvv: async function(){
            try {
                const response = await payform.createElement(
                  'card-cvc', {
                    style: this.elementStyles,
                    placeholder: 'CVV'
                  }, 'txtCvv'
                );
                console.log('Resultado Cvv:', response);
              } catch (error) {
                console.error('Error al crear el elemento:', error);
              }
        },

        sendPaymentVal:  function(sessionToken, merchantId, totals) {
            var data = {
                name: 'nombre',
                lastName: 'nombre',
                email: this.getCustomerDetails().email
            };

            window.configuration = {
                sessionkey: sessionToken,
                channel: 'web',
                callbackurl : '',
                merchantid: merchantId,
                purchasenumber: '3543535',
                amount: String(totals),
                language: 'es',
                font: 'https://fonts.googleapis.com/css?family=Montserrat:400&display=swap',
            };

            window.payform.purchase = '3543535';
            window.payform.dcc = false;
            window.payform.setConfiguration(window.configuration);

            try {
                // Esperar a que los elementos de tarjeta se creen
                // window.cardNumber = await this.createCard();
                // window.cardExpiry = await this.createExpiry();
                // window.cardCvv = await this.createCvv();

                var elementStyles= {
                    base: {
                        color: 'black',
                        margin: '0',
                        fontFamily: "'Montserrat', sans-serif",
                        fontSmoothing: 'antialiased',
                        placeholder: {
                        color: '#999999'
                        },
                        autofill: {
                        color: '#e39f48',
                        }
                    },
                    invalid: {
                        color: '#E25950',
                        '::placeholder': {
                        color: '#FFCCA5',
                        }
                    }
                    };

                window.cardNumber =  window.payform.createElement(
                    'card-number', {
                        style: elementStyles,
                        placeholder: 'Número de Tarjeta'
                    },
                    'txtNumeroTarjeta'
                    ).then(element => {
                        element.on('change', function(data) {
                            console.log('cardNumber: ', data);
                        })
                    });
                
                console.log('cardNumber', window.cardNumber);
              
                
                window.cardExpiry =  window.payform.createElement(
                    'card-expiry', {
                        style: elementStyles,
                        placeholder: 'mmaa'
                    },
                    'txtFechaVencimiento'
                    ).then(element => {
                        element.on('change', function(data) {
                            console.log('Expiry: ', data);
                        })
                        });
                

                window.cardCvv =  window.payform.createElement(
                    'card-cvc', {
                        style: elementStyles,
                        placeholder: 'cvc'
                    },
                    'txtCvv'
                    ).then(element => {
                        element.on('change', function(data) {
                            console.log('CHANGE CVV2: ', data);
                        })
                    });
                      
          

                var cardNumber = this.getSelector('cc_number');
                var cardExpiry = this.getSelector('expirationDate');
                var cardCvc = this.getSelector('cc_cid');

                console.log('hola');
                
                // Crear el token con los elementos de tarjeta
                const response = window.payform.createToken([window.cardNumber, window.cardExpiry, window.cardCvv], data);
                 return response;
            } catch (error) {
                console.log('Error:');
            }
        },

        // sendPaymentVal: function(sessionToken,merchantId,totals) {

        //     var data = {
        //         // name: this.getCustomerDetails().firstName,
        //         // lastName: this.getCustomerDetails().lastname,
        //         name    :'nombre',
        //         lastName:'nombre',
        //         email: this.getCustomerDetails().email
        //         };

        //     var configuration = {
        //         sessionkey: sessionToken,
        //         channel: 'web',
        //         merchantid: merchantId,
        //         purchasenumber: 3543535,
        //         amount: totals,
        //         language: 'es',
        //         font: 'https://fonts.googleapis.com/css?family=Montserrat:400&display=swap',
        //         };

        //         window.payform.purchase = 3543535;
        //         window.payform.dcc = false;
        //         window.payform.setConfiguration(configuration);


        //         window.cardNumber = this.createCard();
        //         window.cardExpiry = this.createExpiry();
        //         window.cardCvv = this.createCvv();



        //         var cardNumber = this.getSelector('cc_number');
        //         var cardExpiry = this.getSelector('expirationDate');
        //         var cardCvc = this.getSelector('cc_cid');

        //         console.log(data);
        //         console.log(configuration);

        //         // return cardNumber+cardExpiry+cardCvc;
        //         /* Caso de uso: Controles independientes */
        //         window.payform.createToken([window.cardNumber,window.cardExpiry,window.cardCvc], data).then(function(response){
        //         return response;
        //             }).catch(function(error){
        //                 globalMessageList.addErrorMessage({
        //                     message: error.message
        //                 });
        //         })
        // },

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
                    let total = quote.getCalculatedTotal();

                    const payment = self.sendPaymentVal(response.token, response.merchant, total);
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
                    fullScreenLoader.stopLoader();

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

