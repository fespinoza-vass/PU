define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'WolfSellers_Checkout/js/model/customer'
], function (
    $,
    ko,
    Form,
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
    return Form.extend({
        defaults: {
            template: 'WolfSellers_Checkout/customer-data',
            customerFormTemplate: 'WolfSellers_Checkout/customer-data/form'
        },
        isVisible: ko.observable(true),
        quoteIsVirtual: quote.isVirtual(),
        isVisibleEdit: ko.observable(false),
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
                if (!value){
                    customer.isCustomerStepFinished('_complete');
                }else{
                    customer.isCustomerStepFinished('_active');
                }
            },this);
            return this;
        },

        /** @inheritdoc */
        initConfig: function () {
            this._super();
            this.namespace = "customerData";
            this.selector = '[data-form-part=' + this.namespace + ']';
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
         * Before next step validate if two forms are correct.
         * @returns void
         */
        navigateToNextStep: function () {
            var emailIsValid = this.validateEmailForm();
            var validatePass = this.validatePassForm();
            var validateConfirmPass   = this.validatePassConfirm();
            this.source.set('params.customerDataStepInvalid', false);
            this.triggerValidationCustomerDataForm();
            if (emailIsValid &&  validatePass && validateConfirmPass && !this.source.get('params.customerDataStepInvalid')) { // Verificar si el formulario es válido
                this.isVisibleEdit(false);
                this.saveCustomerData();
                stepNavigator.next();
            }
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
                telephoneValidator =registry.get("checkout.steps.customer-data-step.customer-fieldsets.customer-data-telefono")

                customer.email(emailValidator.email() === '' ? customer.email() : emailValidator.email());
                customer.customerName(nameValidator.value());
                customer.customerLastName(lastnameValidator.value());
                customer.customerTypeIdentification(typeIdentificationValidator.value());
                customer.customerNumberIdentification(numberIdentificationValidator.value());
                customer.customerTelephone(telephoneValidator.value());
                customer.passwordRegister(emailValidator.passwordRegister());
                customer.passwordConfirm(emailValidator.passwordConfirm());
        },

        /**
         * Show/Edit customer personal information
         */
        editPersonalInfo: function (){
            stepNavigator.navigateTo("customer_step");
            this.isVisibleEdit(true);
        },
        /**
         * Trigger Customer data Step data validate event.
         */
        triggerValidationCustomerDataForm: function () {
            this.source.trigger('customerData.firstname.data.validate'); // Disparar validación
            this.source.trigger('customerData.lastname.data.validate'); // Disparar validación
            this.source.trigger('customerData.identificacion.data.validate'); // Disparar validación
            this.source.trigger('customerData.numero_de_identificacion.data.validate'); // Disparar validación
            this.source.trigger('customerData.telefono.data.validate'); // Disparar validación
        },
        /**
         * Validate only email form
         * @returns {boolean}
         */
        validateEmailForm:function () {

            var emailComponent = registry.get("checkout.steps.customer-data-step.customer-email");
            if (!emailComponent.validateEmail()){
                //NOTICE Email component validate with jquery
                $("form[data-role='email-with-possible-login']").submit();
                return false
            }
            return true;
        },

        /**
         * Validate only password register form
         * @returns {boolean}
         *
         */
        validatePassForm:function () {
            var emailComponent = registry.get("checkout.steps.customer-data-step.customer-email");
            if (quote.guestEmail !== null){
                if (_.isUndefined(emailComponent.passwordRegister())) {
                    $("form[data-role='email-with-possible-login']").submit();
                    return false;
                } else {
                    $("form[data-role='email-with-possible-login']").submit();
                    return true;
                }
            } else {
                $("form[data-role='email-with-possible-login']").submit();
                return true;
            }
        },

        /**
         * Validate only password confirm form
         * @returns {boolean}
         *
         */
        validatePassConfirm: function (){
            var emailComponent = registry.get("checkout.steps.customer-data-step.customer-email");
            if (quote.guestEmail !== null){
                if (_.isUndefined(emailComponent.passwordConfirm())) {
                    $("form[data-role='email-with-possible-login']").submit();
                    return false;
                }else {
                    $("form[data-role='email-with-possible-login']").submit();
                    return true;
                }
            } else {
                 $("form[data-role='email-with-possible-login']").submit();
                 return true;
            }
        }
    });
});
