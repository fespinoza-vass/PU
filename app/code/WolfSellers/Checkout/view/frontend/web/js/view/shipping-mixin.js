define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer'
],function (
    $,
    ko,
    _,
    registry,
    stepNavigator,
    $t,
    quote,
    shippingPayment,
    customer
) {
    'use strict';
    var shippingAddressPath = "checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.";
    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
            links: {
                "goToResume":'checkout:isVisibleShipping'
            }
        },
        isActive: ko.observable(false),
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),
        isRegularShipping:ko.observable(),
        isFastShipping:ko.observable(),
        shippingMethod:ko.observable(),
        goToResume:ko.observable(),

        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            this.setIsDisabledShippingStep();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                this.setIsDisabledShippingStep();
            },this);
            this.goToResume.subscribe(function (value) {
                console.log("hola");
            },this);
            return this;
        },
        /**
         * Overwrite set shipping information action
         * @returns {*}
         */
        setShippingInformation:function () {
            this.setDataToShippingForm();
            if (this.validateShippingInformation()) {
                this.isShippingStepFinished("_complete");
                this.goToResume(false);
            }else{
                this.isShippingStepFinished("_active");
                this.goToResume(true);
            }
            return this._super();
        },
        /**
         * Update progress bar to complete or incomplete state
         */
        setIsDisabledShippingStep: function () {
            if (customer.isCustomerStepFinished() === '_complete'){
                this.isDisabledShippingStep(true);
            }else{
                this.isDisabledShippingStep(false);
            }
        },
        /**
         * Set shipping method flatRate for regular shipping
         * @param e
         * @param t
         */
        setRegularShipping: function (e,t) {
            if(t.currentTarget){
                this.isRegularShipping(true);
                this.isFastShipping(false);
                this.setDataToShippingForm();
                var rate = this.findRateByCarrierCode('flatrate');
                this.selectShippingMethod(rate);
            }
        },
        /**
         * Set shipping method for fast shipping
         * @param e
         * @param t
         */
        setFastShipping:function (e,t) {
            if(t.currentTarget){
                this.isRegularShipping(false);
                this.isFastShipping(true);
                var rate = this.findRateByCarrierCode('envio_rapido');
                this.selectShippingMethod(rate);
            }
        },
        /**
         * Find a rate or shipping Method by carrier_code
         * @param carrierCode
         * @returns {*}
         */
        findRateByCarrierCode:function (carrierCode) {
            return _.find(this.rates(), { 'carrier_code': carrierCode });
        },
        /**
         * Set data for shipping inputs by array uiComponents
         * Todo its not finished needs to be clear
         */
        setDataToShippingForm: function () {
            var validationRuleName = 'required-entry';
            var newValidationConfig = {
                [validationRuleName]: false
            };
            var uiComponentsRequired = ["firstname","lastname", "telephone"];
            var uiComponent = this.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.firstname.value(customer.customerName());
            uiComponent.lastname.value(customer.customerLastName());
            uiComponent.telephone.value(customer.customerTelephone());
            uiComponentsRequired = ["vat_id","distrito_envio_rapido","invoice_required","company","dni"];
            uiComponent = this.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.vat_id.validation = Object.assign({}, uiComponent.vat_id.validation, newValidationConfig);
            uiComponent.distrito_envio_rapido.validation = Object.assign({}, uiComponent.distrito_envio_rapido.validation, newValidationConfig);
            uiComponent.invoice_required.validation = Object.assign({}, uiComponent.invoice_required.validation, newValidationConfig);
            uiComponent.company.validation = Object.assign({}, uiComponent.company.validation, newValidationConfig);
            uiComponent.dni.validation = Object.assign({}, uiComponent.company.dni, newValidationConfig);

        },
        /**
         * get uiComponentsByPath + [names]
         * @param path
         * @param uiComponentsRequired
         * @returns {*}
         */
        getUiComponentsArray: function (path, uiComponentsRequired) {
            return _.chain(uiComponentsRequired)
                .map(function(componentName) {
                    var component = registry.get(path + componentName);
                    return component ? [componentName, component] : null;
                })
                .compact()
                .object()
                .value();
        }
    }

    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }
});
