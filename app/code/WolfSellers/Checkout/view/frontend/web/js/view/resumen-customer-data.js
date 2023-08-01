define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'WolfSellers_Checkout/js/model/customer'
], function (
    ko,
    Component,
    _,
    quote,
    registry,
    customer
) {
    'use strict';

    /**
     * Customer Data Resumen Component
     */
    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/customer-data/resumen-customer-data'
        },

        isVisible: ko.observable(true),
        email: ko.observable(customer.email()),
        customerName: ko.observable(customer.customerName()),
        customerLastName: ko.observable(customer.customerLastName()),

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();

            customer.email.subscribe(function (value) {
                this.email(value);
            }, this);

            customer.customerName.subscribe(function (value) {
                this.customerName(value);
            }, this);

            return this;
        }

    });
});
