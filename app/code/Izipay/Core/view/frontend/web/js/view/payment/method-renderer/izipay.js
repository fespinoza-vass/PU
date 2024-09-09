define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_CheckoutAgreements/js/model/agreements-assigner',
        'Magento_Checkout/js/model/error-processor',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/url',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/customer'
    ],
    function (Component, agreementsAssigner, errorProcessor, $, quote, redirectOnSuccessAction, url, additionalValidators, fullScreenLoader, customer ) {
        'use strict';
 
        return Component.extend({
            defaults: {
                template: 'Izipay_Core/payment/izipay'
            },
            redirectAfterPlaceOrder: true,

            getAlternativePaymentMethods: function() {
                let alternative_payment_methods = window.checkoutConfig.payment.izipay.alternative_payment_methods;                
                return alternative_payment_methods;
            },
            getTypeForm: function() {
                let izipay_type_form = window.checkoutConfig.payment.izipay.appearence.type_form;               
                return izipay_type_form;
            },
            callEventsDefault: function() {
                var self = this;
                $(function(){
                    var content_razon_social_izipay = $('.content_razon_social_izipay');
                    var razon_social_izipay = $('[name="izipay_razon_social"]');
                    var document_type = $('[name="izipay_document_type"]');
                    
                    document_type.on('change', function() {
                        if (this.value == "RUC") {
                            content_razon_social_izipay.show();
                            razon_social_izipay.val("");
                        } else {
                            content_razon_social_izipay.hide();
                            razon_social_izipay.val("");
                        }
                    });

                    $('input[type=radio][name=izipay-alternative-payment]').change(function() {
                        if (self.getTypeForm() == "embedded") {
                            fullScreenLoader.startLoader();
                            $("#izipay-iframe-payment").html("");
                            $("#action-tool-bar-content-izi").show();
                            self.generateToken(function(){
                                fullScreenLoader.stopLoader();
                            });
                        }
                    });
                });
            },
            generateToken: function(callback) {

                var quoteId = quote.getQuoteId();
                var result=false;

                var url_token = url.build('izipay/payment/token');
                var payload = {
                    'cartId': quoteId
                };
                $.ajax({
                    url: url_token,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function (response) {
                        var data = JSON.parse(response);
                        if (data.code == "00") {
                            $("[name='izipay_token_code']").val(data.code);
                            $("[name='izipay_token']").val(data.response.token);
                            $("[name='izipay_transaction_id']").val(data.transaction_id);
                            $("[name='izipay_order_number']").val(data.order_number);
                        }
                        result = true;
                        typeof callback === 'function' && callback();
                    }
                ).fail(
                    function (response) {
                        result = false;
                        typeof callback === 'function' && callback();
                        errorProcessor.process(response);
                    }
                );
            },
            saveLogIzipay : function(type, request, response, status) {
                var quoteId = quote.getQuoteId();
                var orderNumber = $("[name='izipay_order_number']").val();

                var url_log = url.build('izipay/payment/log');
                var payload = {
                    'cartId': quoteId,
                    'order_number': orderNumber,
                    'type': type,
                    'request': request,
                    'response': response,
                    'status': status,
                };
                $.ajax({
                    url: url_log,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function (response) {
                        console.log(response);
                    }
                );
            },
            saveExtraDataQuote : function(callback) {
                var isCustomer = customer.isLoggedIn();
                var quoteId = quote.getQuoteId();
                var izipay_alternative_payment_method = $('[name="izipay-alternative-payment"]:checked').val();
                var izipay_document_type = $('[name="izipay_document_type"]').val();
                var izipay_document_number = $('[name="izipay_document_number"]').val();
                var razon_social_izipay = $('[name="izipay_razon_social"]').val();
                
                var izipay_transaction_id = $('[name="izipay_transaction_id"]').val();
                var izipay_order_number = $('[name="izipay_order_number"]').val();
                var izipay_payment_code_response = $('[name="izipay_payment_code_response"]').val();

                var payload = {
                    'cartId': quoteId,
                    'izipay_alternative_payment_method': izipay_alternative_payment_method,
                    'izipay_document_type': izipay_document_type,
                    'izipay_document_number': izipay_document_number,
                    'izipay_razon_social' : razon_social_izipay,

                    'izipay_transaction_id': izipay_transaction_id,
                    'izipay_order_number': izipay_order_number,
                    'izipay_payment_code_response': izipay_payment_code_response,

                    'is_customer': isCustomer
                };

                var result = true;
                var url_post = url.build('izipay/quote/save');

                $.ajax({
                    url: url_post,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function (response) {
                        result = true;
                        typeof callback === 'function' && callback();
                    }
                ).fail(
                    function (response) {
                        result = false;
                        typeof callback === 'function' && callback();
                        errorProcessor.process(response);
                    }
                );
            },
            placeOrderIzipay : function() {
                var self = this;
                console.log("Open izipay");

                // Si todo es valido entra.
                if(additionalValidators.validate()) {

                    if (self.getTypeForm() == "embedded") {
                        $("#action-tool-bar-content-izi").hide();
                    }

                    var izipay_token_code = $("[name='izipay_token_code']").val();
                    var izipay_token = $("[name='izipay_token']").val();
                    var izipay_transaction_id = $("[name='izipay_transaction_id']").val();
                    var izipay_order_number = $("[name='izipay_order_number']").val();

                    if (izipay_token_code != "00") {
                        alert("No se generó el token correctamente.");
                    } else {
                        // Agregar lógica para levantar popup de izipay frontend.
                        var izipay_theme = window.checkoutConfig.payment.izipay.appearence.theme;
                        var izipay_style_input = window.checkoutConfig.payment.izipay.appearence.style_input;
                        var izipay_type_form = window.checkoutConfig.payment.izipay.appearence.type_form;
                        var izipay_logo = window.checkoutConfig.payment.izipay.appearence.logo;
                        var logo_izipay_config = window.location.origin+'/media/izipay/'+izipay_logo;
                        console.log(logo_izipay_config)

                        var izipay_notification_url = window.checkoutConfig.payment.izipay.notification_url;
                        var izipay_type_access = window.checkoutConfig.payment.izipay.type_access;
                        var izipay_public_key = window.checkoutConfig.payment.izipay.public_key;
                        var merchant_code = window.checkoutConfig.payment.izipay.merchant_code;

                        var order_currency = "PEN";
                        if (window.checkoutConfig.quoteData.base_currency_code != "PEN") {
                            order_currency = "USD";
                        }
                        
                        var amount = quote.totals._latestValue.grand_total;
                        var izipay_email_user = "";
                        var merchantBuyerId = "";
                        if (window.isCustomerLoggedIn) {
                            izipay_email_user = window.checkoutConfig.customerData.email;
                            merchantBuyerId = window.checkoutConfig.customerData.id;
                        } else {
                            izipay_email_user = quote.guestEmail;
                            merchantBuyerId = quote.guestEmail;
                        }

                        var izipay_document_type = $("[name='izipay_document_type']").val();
                        var izipay_document_number = $("[name='izipay_document_number']").val();
                        var izipay_company_name = $("[name='izipay_razon_social']").val();

                        var billingData = quote.shippingAddress._latestValue;

                        var alternative_payment_method_selected = $('[name="izipay-alternative-payment"]:checked').val();
                        var has_izipay_alternative_methods = $('[name="has-izipay-alternative-methods').val();
                        if (has_izipay_alternative_methods=="false") {
                            alternative_payment_method_selected="all";
                        }
                        var street_full = billingData.street[0]+" "+billingData.street[1];

                        /*var postal_code = billingData.postcode;
                        if (postal_code == undefined) {
                            postal_code = "00051";
                        }*/
                        var postal_code = "00051";
                        var dateTimeIzi = Math.floor(Date.now()) * 1000;

                        var customAttributes = billingData.customAttributes;

                        var colonyValue = null;

                        for (var i = 0; i < customAttributes.length; i++) {
                            if (customAttributes[i].attribute_code === 'colony') {
                                colonyValue = customAttributes[i].value;
                                break;
                            }
                        }

                        const iziConfig = {
                            publicKey: izipay_public_key,
                            config: {
                                transactionId: izipay_transaction_id.toString(),
                                action: 'pay',
                                merchantCode: merchant_code,
                                order: {
                                    orderNumber: izipay_order_number.toString(),
                                    currency: order_currency,
                                    amount: amount,
                                    processType: izipay_type_access,
                                    payMethod : alternative_payment_method_selected,
                                    merchantBuyerId: merchantBuyerId,
                                    dateTimeTransaction: dateTimeIzi.toString(), //currentTimeUnix
                                },
                                card: {
                                    brand: '',
                                },
                                billing: {
                                    firstName: billingData.firstname.normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
                                    lastName: billingData.lastname.normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
                                    email: izipay_email_user,
                                    phoneNumber: billingData.telephone,
                                    street: street_full.substring(0,40),
                                    city: billingData.city.normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
                                    state: colonyValue.normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
                                    country: billingData.countryId,
                                    postalCode: postal_code,
                                    document: izipay_document_number,
                                    documentType: izipay_document_type,
                                    companyName: izipay_company_name.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                                },
                                language: {
                                    init: "ESP",
                                    showControlMultiLang: false,
                                },
                                render: {
                                    typeForm: izipay_type_form,
                                    container: '#izipay-iframe-payment',
                                },
                                //urlRedirect:'https://server.punto-web.com/comercio/creceivedemo.asp?p=h1',
                                urlRedirect:window.location.origin+"/izipay/payment/notificationredirect",
                                appearance: {
                                    logo: logo_izipay_config,
                                    styleInput: izipay_style_input,
                                    theme: izipay_theme
                                },
                            },
                        };

                        const callbackResponsePayment  = function(response) {

                            $(".error-validation-izipay-document-number").hide();
                            // Guardamos en el log
                            var responseIzi = JSON.stringify(response);
                            self.saveLogIzipay("Form Izipay Response", "", responseIzi, response.code);
                            $("[name='izipay_payment_code_response']").val(response.code);

                            if (response.code == "00") {
                                $("#btnPlaceOrderOriginal").click();
                            } else if (response.code == "021") {
                                fullScreenLoader.startLoader();
                                self.generateToken(function(){
                                    fullScreenLoader.stopLoader();
                                });
                            } else if (response.code == "P54") {
                                fullScreenLoader.startLoader();
                                self.generateToken(function(){
                                    fullScreenLoader.stopLoader();
                                });
                                self.placeOrderIzipay();
                            } else {
                                console.log(response);

                                if (response.response) {
                                    const obj_response = JSON.parse(response.response.payloadHttp);
                                    console.log(obj_response);
    
                                    if (obj_response.response.hasOwnProperty("dateTransaction")) {
                                        //mostrar popup de error
                                        var modal = $(".modal-izipay-result");
                                        modal.addClass("open");
                                        const exit = $(".modal-izipay-result .modal-exit");
                                        exit.on("click", function(){
                                            modal.removeClass("open");
                                        });
                                        // llenar campos de popup de error.
                                        var date_full = obj_response.response.dateTransaction;
                                        var hora_full = obj_response.response.timeTransaction;
                                        var fecha_response = date_full.substring(6, 8)+"-"+date_full.substring(4, 6)+"-"+date_full.substring(0, 4);
                                        var hora_response = hora_full.substring(0, 2)+":"+hora_full.substring(2, 4)+":"+hora_full.substring(4, 6);
                                        $("#result_izipay_code").html(obj_response.code);
                                        $("#result_izipay_message").html(obj_response.message);
                                        $("#result_izipay_moneda").html(obj_response.response.currency);
                                        $("#result_izipay_monto").html(obj_response.response.amount);
                                        $("#result_izipay_order_number").html(obj_response.response.orderNumber);
                                        $("#result_izipay_codeauth").html(obj_response.response.codeAuth);
                                        $("#result_izipay_fecha_hora").html(fecha_response+" "+hora_response);
                                        $("#result_izipay_metodo_pago").html(obj_response.response.payMethod);                                        
                                    }
                                    fullScreenLoader.startLoader();
                                    self.generateToken(function(){
                                        fullScreenLoader.stopLoader();
                                    });
                                    if (self.getTypeForm() == "embedded") {
                                        $("#action-tool-bar-content-izi").show();
                                    }
                                }
                            }
                        }

                        var requestIzi = JSON.stringify(iziConfig);
                        self.saveLogIzipay("Form Izipay Request", requestIzi, "", "");

                        try {
                            if (self.getTypeForm() == "redirect") {
                                fullScreenLoader.startLoader();
                                self.saveExtraDataQuote(function(){
                                    fullScreenLoader.stopLoader();
                                    const izi = new Izipay({
                                        publicKey: iziConfig?.publicKey,
                                        config: iziConfig?.config,
                                    });
                    
                                    izi && izi.LoadForm({
                                        authorization: izipay_token,
                                        keyRSA: 'RSA',
                                        callbackResponse: callbackResponsePayment,
                                    });
        
                                    $("#error-izipay-result").hide();
                                });
                            } else {
                                const izi = new Izipay({
                                    publicKey: iziConfig?.publicKey,
                                    config: iziConfig?.config,
                                });
                
                                izi && izi.LoadForm({
                                    authorization: izipay_token,
                                    keyRSA: 'RSA',
                                    callbackResponse: callbackResponsePayment,
                                });
    
                                $("#error-izipay-result").hide();
                            }
                        } catch (error) {
                            console.log("IZIPAY EXCEPTION:")
                            console.log(error);
                            console.log(error.message, error.Errors, error.date);

                            var html_error = "<ul>";
                            for (var item in error.Errors.entryData) {
                                html_error = html_error + "<li>";
                                html_error = html_error + "<b>Error en "+item+" :</b><br/>";
                                html_error = html_error + error.Errors.entryData[item].message;
                                html_error = html_error + "</li>";
                            }
                            html_error = html_error + "</ul>";

                            $("#error-izipay-result").html(html_error);
                            $("#error-izipay-result").show();
                            
                            var requestIzipay = JSON.stringify(iziConfig);
                            var responseError = JSON.stringify(error.Errors);
                            self.saveLogIzipay("Form Izipay Error", requestIzipay, responseError, "");

                            if (self.getTypeForm() == "embedded") {
                                $("#action-tool-bar-content-izi").show();
                            }
                        }

                    }
                }
            },
            afterPlaceOrder: function () {
                //redirectOnSuccessAction.redirectUrl = url.build('izipay/payment/index');
                //this.redirectAfterPlaceOrder = true;
            }
        });
    }
);