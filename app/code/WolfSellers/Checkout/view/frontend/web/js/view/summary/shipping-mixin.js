/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'mage/url'
], function (ko, wrapper, quote, $,url) {
    'use strict';

    var mixin = {
        customTitle: ko.observable(''),
        initialize : function () {
          var self = this;
          this._super();
          quote.shippingMethod.subscribe(function (value) {
            if (value && value.method_title) {
                var payload = {
                    'ubigeo': quote.shippingAddress().postcode
                };
                $.ajax({
                    type: 'POST',
                    url: url.build('zipcode/index/getubigeo'),
                    dataType: 'json',
                    data: payload,
                    global: false
                }).done(function (estimated) {
                    if (estimated) {
                        estimated = JSON.parse(estimated);
                        self.customTitle(value['method_title'] + ' ' + estimated.data );
                    } else {
                        self.customTitle(value['method_title']);
                    }
                })
            }


          });
        quote.shippingAddress.subscribe(function (value) {
            var _shippingMethod = quote.shippingMethod();

            if (_shippingMethod &&_shippingMethod.method_title) {
                var payload = {
                    'ubigeo': quote.shippingAddress().postcode
                };
                $.ajax({
                    type: 'POST',
                    url: url.build('zipcode/index/getubigeo'),
                    dataType: 'json',
                    data: payload,
                    global: false
                }).done(function (estimated) {
                    if (estimated) {
                        self.customTitle(_shippingMethod['method_title'] + ' ' + estimated.data );
                    } else {
                        self.customTitle(_shippingMethod['method_title']);
                    }
                })
                }
            });
        },
        getShippingMethodTitle: function () {
            var shippingMethod = '',
                shippingMethodTitle = '';

            if (!this.isCalculated()) {
                return '';
            }
           return this.customTitle();




        },

    };

    /**
     * Override default getShippingMethodTitle
     */
    return function (OriginShipping) {
        return OriginShipping.extend(mixin);
    };
});
