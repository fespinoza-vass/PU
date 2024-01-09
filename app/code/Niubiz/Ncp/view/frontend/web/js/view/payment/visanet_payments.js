
/*browser:true*/
/*global define*/
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
                type: 'Ncp_pay',
                component: 'Niubiz_Ncp/js/view/payment/method-renderer/Ncp_method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
