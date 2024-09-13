define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (billingAddress) {
        billingAddress.updateAddress = wrapper.wrapSuper(billingAddress.updateAddress, function () {
            console.log('Custom logic before updateAddress');
            this._super();
            console.log('Custom logic after updateAddress');
        });

        return billingAddress;
    };
});
    