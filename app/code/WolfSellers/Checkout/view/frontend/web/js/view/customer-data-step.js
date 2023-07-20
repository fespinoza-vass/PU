define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'uiRegistry'
], function (
    ko,
    Component,
    _,
    stepNavigator,
    quote,
    registry
) {
    'use strict';

    /**
     * Customer Data Step Component
     */
    return Component.extend({
        defaults: {
            template: 'WolfSellers_Checkout/customer-data',
            customerFormTemplate: 'WolfSellers_Checkout/customer-data/form'
        },

        // add here your logic to display step,
        isVisible: ko.observable(true),
        quoteIsVirtual: quote.isVirtual(),

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();
            stepNavigator.registerStep(
                'customer_step',
                null,
                'Customer Data',
                this.isVisible,
                _.bind(this.navigate, this),
                1
            );


            return this;
        },

        /**
         * The navigate() method is responsible for navigation between checkout steps
         */
        navigate: function () {
            //add logic
            this.isVisible(true);
        },

        /**
         * @returns void
         */
        navigateToNextStep: function () {
            stepNavigator.next();
        }
    });
});
