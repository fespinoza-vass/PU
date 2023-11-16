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
