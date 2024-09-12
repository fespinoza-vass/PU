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

            if (pathUrl.startsWith('/' + pathLogin)) {
                logEvent(paramMobile, 'loginSuccess', {
                    name: 'Luis',
                    lastName: 'Gonzalez',
                    cartQty: 2,
                });
            } else if (pathUrl.startsWith('/' + pathRegister)) {
                logEvent(paramMobile, 'RegisterSuccess', {
                    name: 'Luis',
                    lastName: 'Gonzalez',
                    cartQty: 2,
                });
            }
        }
    });

    function logEvent(platform, command, params) {
        console.log('logEvent', platform, command, params);
        if (typeof window === 'undefined') {
            return
        }

        switch (platform) {
            case 'android':
                if (window.AppFacade[command]) {
                    console.error('Error: AppFacade not found')
                } else {
                    window.AppFacade[command](JSON.stringify(params))
                }
                break
            case 'ios':
                if (!window.webkit?.messageHandlers?.AppFacade?.postMessage) {
                    console.error('Error: AppFacade not found')
                } else {
                    window.webkit.messageHandlers.AppFacade.postMessage({command, ...params})
                }
                break
        }
    }

});
