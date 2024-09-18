define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Notifies Google Tag Manager about a purchase event.
     *
     * @param {Object} data - The data object containing the order information.
     * @param {Object} data.order - The order object.
     * @param {string} data.order.actionField - The action field of the purchase.
     * @param {Array} data.order.products - The array of products in the purchase.
     * @param {string} data.order.currencyCode - The currency code of the purchase.
     * @return {void} This function does not return anything.
     */
    function notifyPurchase(data) {
        const order = data.order;

        let purchaseData = {
            'event': 'purchase',
            'ecommerce': {
                'purchase': {
                    'actionField': order.actionField,
                    'products': order.products
                },
                'currencyCode': order.currencyCode
            }
        };

        window.dataLayer.push(purchaseData);
    }

    return function (data) {
        if (window.dataLayer) {
            notifyPurchase(data);
        } else {
            $(document).on('ga:inited', notifyPurchase.bind(this, data));
        }
    };
});
