/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Customer/js/model/customer',
    'Magento_GiftCardAccount/js/model/payment/gift-card-messages',
    'mage/utils/wrapper',
    'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry'
], function (
    $,
    storage,
    quote,
    urlBuilder,
    errorProcessor,
    fullScreenLoader,
    getPaymentInformationAction,
    totals,
    customer,
    messageList,
    wrapper,
    recaptchaRegistry
) {
    'use strict';

    return function (setGiftCardAction) {
        return wrapper.wrap(setGiftCardAction, function (originalAction, giftCardCode) {
            var serviceUrl,
                payload,
                headers = {},
                message = $.mage.__('Gift Card %1 was added.').replace('%1', giftCardCode);

            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/carts/guest-carts/:cartId/giftCards', {
                    cartId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    giftCardAccountData: {
                        'gift_cards': giftCardCode
                    }
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/giftCards', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    giftCardAccountData: {
                        'gift_cards': giftCardCode
                    }
                };
            }

            if (recaptchaRegistry.triggers.hasOwnProperty('recaptcha-checkout-gift-apply')) {
                recaptchaRegistry.addListener('recaptcha-checkout-gift-apply', function (token) {
                    headers['X-ReCaptcha'] = token;
                });

                return storage.post(
                    serviceUrl, JSON.stringify(payload), true, 'application/json', headers
                ).done(function (response) {
                    /**
                     * Callback for getPaymentInformationAction.
                     */
                    var onGetPaymentInformationAction = function () {
                        totals.isLoading(false);
                    },
                        deferred = $.Deferred();

                    if (response) {
                        totals.isLoading(true);
                        $.when(getPaymentInformationAction(deferred)).done(onGetPaymentInformationAction);
                        messageList.addSuccessMessage({
                            'message': message
                        });
                    }
                }).fail(function (response) {
                    totals.isLoading(false);
                    errorProcessor.process(response, messageList);
                }).always(function () {
                    fullScreenLoader.stopLoader();
                });
            }

            //No ReCaptcha, just sending the request
            return originalAction(giftCardCode);
        });
    };
});
