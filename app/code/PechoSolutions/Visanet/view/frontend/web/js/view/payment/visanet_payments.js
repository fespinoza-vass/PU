
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
                type: 'visanet_pay',
                component: 'PechoSolutions_Visanet/js/view/payment/method-renderer/visanet_method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
