/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/view/customer',
    'Magento_GoogleTagManager/js/google-tag-manager'
], function ($, customerData) {
    'use strict';

    /**
     * Dispatch product detail event to GA
     *
     * @param {Object} data - product data
     *
     * @private
     */
    function notify(data) {

        const cart = customerData.get('cart')();
        const customer = customerData.get('customer')();
        const product = data.product;
        const cartContent = !$.isEmptyObject(cart) ? {
            'totals': {
                'applied_coupons': [],
                'discount_total': 0,
                'subtotal': parseFloat(cart.subtotalAmount),
                'total': parseFloat(cart.subtotalAmount)
            },
            'items': []
        }:{};

        let lastName = customer.fullname?.replace(customer.firstname, '').trim() ?? '';
        const dataUser = !$.isEmptyObject(customer) ? {
            'email': customer.email,
            'first_name': customer.firstname,
            'Last_name': lastName
        }:{};

         let info = {
            'event': "view_item",
            'pagePostAuthor': "Perfumerias Unidas",
            'ecomm_pagetype': "Product",
            'ecomm_prodid': product.id,  //ID de producto
            'ecomm_totalvalue': parseFloat(product?.totalValue ?? 0).toFixed(2),
            'cartContent': cartContent,
            'ecommerce': {
                'currencyCode': data.currencyCode,
                'detail': {
                    'products': [ product ],
                },
            },
            'dataUser': dataUser
        };

        window.dataLayer.push(info);
    }

    return function (productData) {
        window.dataLayer ?
            notify(productData) :
            $(document).on('ga:inited', notify.bind(this, productData));
    };
});
