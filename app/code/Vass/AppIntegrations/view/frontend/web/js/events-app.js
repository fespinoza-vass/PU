/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AppIntegrations
 * @author Vass Team
 */

define([
    'jquery',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, url, customerData) {
    'use strict';

    $(document).ready(function () {
        let queryString = window.location.search;
        let urlParams = new URLSearchParams(queryString);
        let pathUrl = window.location.pathname;
        let paramMobile = urlParams.get('mobile');

        if (!paramMobile) {
            if (pathUrl.includes('/mobile/android')) {
                paramMobile = 'android';
            } else if (pathUrl.includes('/mobile/ios')) {
                paramMobile = 'ios';
            }
        }

        if (paramMobile) {
            let pathLogin = 'customer/app/login';
            let pathRegister = 'customer/app/register';
            let customer = customerData.get('customer');
            let cart = customerData.get('cart');
            var customerName = '', customerLastName = '', cartQty = 0;

            if (cart().summary_count) {
                cartQty = cart().summary_count;
            }

            // Login and Register events
            customer.subscribe(function () {
                setTimeout(function () {
                    if (customer().fullname) {
                        customerName = customer().firstname ?? '';
                        customerLastName = customer().fullname.replace(customerName, '').trim() ?? '';
                    }

                    if (pathUrl.startsWith('/' + pathLogin)) {
                        logEvent(paramMobile, 'loginSuccess', {
                            name: customerName,
                            lastName: customerLastName,
                            cartQty: cartQty,
                        });
                    } else if (pathUrl.startsWith('/' + pathRegister)) {
                        logEvent(paramMobile, 'registerSuccess', {
                            name: customerName,
                            lastName: customerLastName,
                            cartQty: cartQty,
                        });
                    }
                }, 300)
            });

            // Cart events
            cart.subscribe(function () {
                if (cart().summary_count !== cartQty) {
                    logEvent(paramMobile, 'updateCart', {
                        qty: cart().summary_count,
                    });
                    cartQty = cart().summary_count;
                }
            });
        }
    });

    function logEvent(platform, command, params) {
        console.log('logEvent', platform, command, params);
        if (typeof window === 'undefined') {
            return
        }

        switch (platform) {
            case 'android':
                window.AppFacade[command](JSON.stringify(params))
                break
            case 'ios':
                window.webkit.messageHandlers.AppFacade.postMessage({command, ...params})
                break
        }
    }

});
