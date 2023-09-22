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
        customerTypeIdentification:ko.observable(customer.customerTypeIdentification()),
        customerNumberIdentification: ko.observable(customer.customerNumberIdentification()),
        customerTelephone:ko.observable(customer.customerTelephone()),
       passwordRegister :ko.observable(customer.passwordRegister()),
        passwordConfirm : ko.observable(customer.passwordConfirm()),

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

            customer.customerLastName.subscribe(function (value) {
                this.customerLastName(value);
            }, this);

            customer.customerTypeIdentification.subscribe(function (value){
                this.customerTypeIdentification(value);
            },this);

            customer.customerNumberIdentification.subscribe(function (value) {
                this.customerNumberIdentification(value);
            },this);

            customer.customerTelephone.subscribe(function (value){
                this.customerTelephone(value);
            },this);

            customer.passwordRegister.subscribe(function (value) {
                this.passwordRegister(value);
            },this);

            customer.passwordConfirm.subscribe(function (value){
                this.passwordConfirm(value);
            },this);

            return this;
        }

    });
});
