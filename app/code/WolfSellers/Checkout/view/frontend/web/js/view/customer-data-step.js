define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'WolfSellers_Checkout/js/model/customer'
], function (
    ko,
    Component,
    _,
    stepNavigator,
    quote,
    registry,
    customer
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
        isVisibleEdit: ko.observable(true),
        isActive: ko.observable(true),
        isEdit: ko.observable(false),
        isEmpty: ko.observable(false),

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();
            stepNavigator.registerStep(
                'customer_step',
                null,
                'Identificación',
                this.isVisible,
                _.bind(this.navigate, this),
                1
            );

            this.isVisibleEdit.subscribe(function (value) {
                console.log(value);
                //this.isActive(value);
            }, this);

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
            this.isVisibleEdit(false);
            this.saveCustomerData();
            stepNavigator.next();
        },

        /**
         * saveCustomerData validate personal information to show in resumen
         */
        saveCustomerData: function (){
            var emailValidator = registry.get("checkout.steps.customer-data-step.customer-email"),
                nameValidator = registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-firstname"),
                lastnameValidator = registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-lastname"),
                typeIdentificationValidator = registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-identificacion"),
                numberIdentificationValidator  =registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-numero_de_identificacion"),
                telephoneValidator =registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-telefono");

                customer.email(emailValidator.email() === '' ? customer.email() : emailValidator.email());
                customer.customerName(nameValidator.value());
                customer.customerLastName(lastnameValidator.value());
                customer.customerTypeIdentification(typeIdentificationValidator.value());
                customer.customerNumberIdentification(numberIdentificationValidator.value());
                customer.customerTelephone(telephoneValidator.value());
        },

        /**
         * Show/Edit customer personal information
         */
        editPersonalInfo: function (){
            stepNavigator.navigateTo("customer_step");
            this.isVisibleEdit(true);
        }
    });
});
