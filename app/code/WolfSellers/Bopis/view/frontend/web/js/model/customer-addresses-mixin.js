define([
    'mage/utils/wrapper',
    'Magento_Customer/js/customer-data'
], function (wrapper, customerData) {
    'use strict';

    return function (defaultProvider) {
        defaultProvider.getAddressItems = wrapper.wrapSuper(defaultProvider.getAddressItems, function () {
            var bopis = customerData.get(['bopis'], true);
            if (bopis().type !== "store-pickup"){
                return this._super();
            }
        });

        return defaultProvider;
    };
});
