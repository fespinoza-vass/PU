/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'mage/url'
], function (ko, _, wrapper, quote, $,url) {
    'use strict';

    var mixin = {
        customTitle: ko.observable(''),
        initialize : function () {
            var self = this;
            this._super();
            quote.shippingMethod.subscribe(function (value) {
                if(!_.isUndefined(value) && !_.isNull(value) && !value.carrier_code.includes('instore')){
                    if (value && value.method_title) {
                        self.customTitle(value['method_title']);
                    }
                }
            });
            quote.shippingAddress.subscribe(function (value) {
                var _shippingMethod = quote.shippingMethod();

                if (_shippingMethod && _shippingMethod.method_title) {
                    self.customTitle(_shippingMethod['method_title']);
                }
            });
        },
        /**
         * disable title that was 15 in the title
         */
        getShippingMethodTitle: function () {
            this._super();
        },
    };

    /**
     * Override default getShippingMethodTitle
     */
    return function (OriginShipping) {
        return OriginShipping.extend(mixin);
    };
});
