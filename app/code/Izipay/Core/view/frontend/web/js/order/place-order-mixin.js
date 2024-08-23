define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_CheckoutAgreements/js/model/agreements-assigner',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'uiRegistry'
], function (
    $, 
    wrapper, 
    agreementsAssigner,
    quote,
    customer,
    urlBuilder, 
    urlFormatter, 
    errorProcessor,
    registry
) {
    'use strict';

    return function (placeOrderAction) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            agreementsAssigner(paymentData);
            var isCustomer = customer.isLoggedIn();
            var quoteId = quote.getQuoteId();

            var url = urlFormatter.build('izipay/quote/save');

            var izipay_payment_active = $('[name="has-izipay-payment-active"]').val();
            
            var izipay_alternative_payment_method = $('[name="izipay-alternative-payment"]:checked').val();
            var izipay_document_type = $('[name="izipay_document_type"]').val();
            var izipay_document_number = $('[name="izipay_document_number"]').val();
            var razon_social_izipay = $('[name="izipay_razon_social"]').val();
            
            var izipay_transaction_id = $('[name="izipay_transaction_id"]').val();
            var izipay_order_number = $('[name="izipay_order_number"]').val();
            var izipay_payment_code_response = $('[name="izipay_payment_code_response"]').val();

            if (izipay_payment_active == "1") {

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

                $.ajax({
                    url: url,
                    data: payload,
                    dataType: 'text',
                    type: 'POST',
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
            }
            
            return originalAction(paymentData, messageContainer);
        });
    };
});