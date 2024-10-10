define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'izipay',
                component: 'Izipay_Core/js/view/payment/method-renderer/izipay'
            }
        );
        return Component.extend({});
    }
);