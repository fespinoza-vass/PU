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
                type: 'vassvisanet',
                component: 'Vass_NiubizVisanet/js/view/payment/method-renderer/vassvisanet-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
