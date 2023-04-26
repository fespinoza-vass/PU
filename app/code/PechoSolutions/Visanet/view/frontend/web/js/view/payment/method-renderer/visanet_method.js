/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        //'Magento_Checkout/js/view/payment/default'
        'Magento_Payment/js/view/payment/cc-form',
        'jquery', 
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer', 
        'Magento_Checkout/js/model/place-order', 
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $, urlBuilder, quote, customer, placeOrderService) {
        'use strict';

        let data = window.checkoutConfig.payment.visanet_pay; 
      //  console.log('Parametros visa recibidos: ', window.checkoutConfig.payment.visanet_pay);

        //console.log("amount:", window.checkoutConfig.payment.visanet_pay.amount*100);
        ///console.log("Genero SecurityKey:", window.checkoutConfig.payment.visanet_pay.securitykey);
        //console.log("Genero SessionToken:", window.checkoutConfig.payment.visanet_pay.sessionToken);
        //console.log("Genero SessionKey",window.checkoutConfig.payment.visanet_pay.publicKey);
        console.log(window.checkoutConfig.payment.visanet_pay.quoteId);


        function getRootUrl() {

            return window.checkout.baseUrl;
        
        }


        return Component.extend({
            defaults: {
                template: 'PechoSolutions_Visanet/payment/form',
                transactionResult: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);


                return this;
            },

            initialize: function(){

                console.log('Iniciando componente VISANET 2');
                console.log('COOKIE MENSAJE CHECkout: ');
                console.log($.cookie('checkout_message'));

this._super(); 
 // Aqui hacer el request para crear el token 

                               console.log('Aqui haremos un request para obtener el token de seguridad');
                               console.log('incio del proceso visa ------');
                               console.log("Guest Email: "+ quote.guestEmail);
                               if(quote.guestEmail) {
                                   var form_data = {
                                       'email' : quote.guestEmail
                                   };
                                   let EmailInvitado = $.ajax({
                                       url: getRootUrl() + 'visanet/visa/email',
                                       type: 'POST',
                                       data: form_data,
                                       success: function (data) {
                                           console.log("Visa Response",data);
                                       }
                                   });
                               }
                               let rqSyncHello = $.ajax({
                                   url: getRootUrl() + 'visanet/visa/hello',
                                   data: JSON.stringify({}),  
                                   showLoader: true,
                                   type: 'GET',
                                   dataType: 'json',
                                   context: this,  
                                   
                                   success : function(response){
                                       console.log(response);
                                       let totals = quote.getTotals()(); 
                                       let grandTotal = 0;
               
                                       console.log('TOTALS: ', totals);
               
                                       if (response.monto) {
                                           grandTotal = response.monto;
                                       }
                                       else{
                                           grandTotal = totals.grand_total;
                                       } 
                                        
                                       let monto = Math.round(grandTotal * 100) / 100;
                                       let vex_show = false;
                                       
                                       if(window.checkoutConfig.payment.visanet_pay.vex_showamount==1){
                                           vex_show = true;
                                       } 
               
                                       console.log('Monto visas: ', monto);
                                       console.log('Visa Checkout:',window.checkoutConfig);
                                       //window.VisanetCheckout.onCI = false
                                       window.VisanetCheckout.configuration = {
                                           action: window.checkoutConfig.payment.visanet_pay.actionUrl,
                                           merchantid: window.checkoutConfig.payment.visanet_pay.merchantId,
                                           channel: "web",
                                           sessiontoken: response.key,
                                           amount: monto,
                                           purchasenumber: window.checkoutConfig.payment.visanet_pay.quote_id,
                                           recurrence : false,
                                           buttonsize : window.checkoutConfig.payment.visanet_pay.vex_buttonsize,
                                           merchantlogo : window.checkoutConfig.payment.visanet_pay.upload_image,
                                           merchantname : '',
                                           formbuttoncolor : window.checkoutConfig.payment.visanet_pay.vex_formbuttoncolor,
                                           showamount : vex_show,
                                           cardholdername : '',
                                           cardholderlastname : '',
                                           cardholderemail : '',
                                           usertoken : '',
                                           frequency : 'Quarterly',
                                           recurrencetype : 'fixed',
                                           recurrenceamount : '200',
                                           documenttype : '0',
                                           timeouturl : getRootUrl()+'checkout',
                                           documentid : '',
                                           beneficiaryid : '',
                                           productid : '',
                                           phone : ''
               
                                       };
                                       
                                       
                                       window.VisanetCheckout.configure(window.VisanetCheckout.configuration);
               
			                
               
               
                                   } 
               
                               });


            },

            divErrorAfterRender: function(){

               //let mensaje =  $.cookie('checkout_message');
               //$('#visa_mensaje_error').html(mensaje);
            },

            getCode: function() {
                return 'visanet_pay';
            },

            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        //'transaction_result': this.transactionResult()
                        //'token': $('#culqi_pay_token').val(),
                        'token': window.checkoutConfig.payment.visanet_pay.sessiontoken,

                    },

                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.visanet_pay.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            },

            loadCheckoutJS: function(){
		window.VisanetCheckout.open();
            },

            //Campos
            getEmail: function () {
                if(quote.guestEmail) {
                    //console.log("Email:", quote.guestEmail);
                    return quote.guestEmail;
                } else {
                    return window.checkoutConfig.customerData.email;
                }
            },
            getSessionKey: function () {
                return window.checkoutConfig.payment.visanet_pay.sessionKey;
            },
            getSessionToken: function () {
                return window.checkoutConfig.payment.visanet_pay.sessionToken;
            },
            getMerchatId: function (){
                return window.checkoutConfig.payment.visanet_pay.merchantId;
            },
            getLogoVisa: function (){
                return window.checkoutConfig.payment.visanet_pay.logo_visa;
            },
            getTerminos: function (){
                return window.checkoutConfig.payment.visanet_pay.terminos;
            },
            getAmount: function (){
                return window.checkoutConfig.payment.visanet_pay.amount*100;
            }
        });
    }
);