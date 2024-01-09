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
        'Magento_Checkout/js/action/set-payment-information',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $, urlBuilder, quote, customer, placeOrderService, setPaymentInformationAction) {
        'use strict';

        let data = window.checkoutConfig.payment.Ncp_pay;

        function getRootUrl() {
            return BASE_URL;
        }

        return Component.extend({
            defaults: {
                template: 'Niubiz_Ncp/payment/form',
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
                this._super();
            },

            divErrorAfterRender: function(){
            },

            getCode: function() {
                return 'Ncp_pay';
            },

            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'token': window.checkoutConfig.payment.Ncp_pay.sessiontoken,
                    },
                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.Ncp_pay.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            },

            loadCheckoutJS: function(){
                var self = this;
               if(quote.guestEmail) {
                   var form_data = {
                       'email' : quote.guestEmail
                   };
                   let EmailInvitado = $.ajax({
                       url: getRootUrl() + 'Ncp/visa/email',
                       type: 'POST',
                       data: form_data,
                       success: function (data) {
                       }
                   });
               }
               let rqSyncHello = $.ajax({
                   url: getRootUrl() + 'Ncp/visa/hello',
                   data: JSON.stringify({}),
                   showLoader: true,
                   type: 'GET',
                   dataType: 'json',
                   context: this,
                   success : function(response){
                       $.when(
                           setPaymentInformationAction(
                               self.messageContainer,
                               self.getData()
                           )
                       ).done(function (){
                           let totals = quote.getTotals()();
                           let grandTotal = 0;

                           if (response.monto) {
                               grandTotal = response.monto;
                           }else{
                               grandTotal = totals.grand_total;
                           }

                           let monto = Math.round(grandTotal * 100) / 100;
                           let vex_show = false;

                           if(window.checkoutConfig.payment.Ncp_pay.vex_showamount==1){
                               vex_show = true;
                           }
                           monto=monto.toFixed(2);
                           window.NcpCheckout.configuration = {
                               action: window.checkoutConfig.payment.Ncp_pay.actionUrl,
                               merchantid: window.checkoutConfig.payment.Ncp_pay.merchantId,
                               channel: "web",
                               sessiontoken: response.key,
                               amount: monto,
                               purchasenumber: window.checkoutConfig.payment.Ncp_pay.quote_id,
                               recurrence : false,
                               buttonsize : window.checkoutConfig.payment.Ncp_pay.vex_buttonsize,
                               merchantlogo : window.checkoutConfig.payment.Ncp_pay.upload_image,
                               merchantname : '',
                               formbuttoncolor : window.checkoutConfig.payment.Ncp_pay.vex_formbuttoncolor,
                               hidexbutton :"TRUE",
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

                           window.NcpCheckout.configure(window.NcpCheckout.configuration);

                           window.NcpCheckout.open();
                       })
                   }
               });
            },

            //Campos
            getEmail: function () {
                if(quote.guestEmail) {
                    return quote.guestEmail;
                } else {
                    return window.checkoutConfig.customerData.email;
                }
            },
            getSessionKey: function () {
                return window.checkoutConfig.payment.Ncp_pay.sessionKey;
            },
            getSessionToken: function () {
                return window.checkoutConfig.payment.Ncp_pay.sessionToken;
            },
            getMerchatId: function (){
                return window.checkoutConfig.payment.Ncp_pay.merchantId;
            },
            getLogoVisa: function (){
                return window.checkoutConfig.payment.Ncp_pay.logo_visa;
            },
            getTerminos: function (){
                return window.checkoutConfig.payment.Ncp_pay.terminos;
            },
            getAmount: function (){
                return window.checkoutConfig.payment.Ncp_pay.amount*100;
            },
            hasBillingAddress(){
                return !!quote.billingAddress();
            }
        });
    }
);
