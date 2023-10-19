define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/checkout-data',
    'domReady!'
],function (
    $,
    ko,
    _,
    registry,
    stepNavigator,
    $t,
    wolfUtils,
    quote,
    shippingPayment,
    customer,
    priceUtils,
    checkoutData
) {
    'use strict';
    var shippingAddressPath = "checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.";
    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
            links: {
                "goToResume":'checkout:isVisibleShipping',
                "updateOptions":"checkout.steps.shipping-step.shippingAddress.schedule.schedule:updateOptions"
            }
        },
        isActive: ko.observable(false),
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),
        isRegularShipping: ko.observable(),
        isFastShipping: ko.observable(),
        isFastShippingDisabled: ko.observable(false),
        isRegularShippingDisabled: ko.observable(false),
        shippingMethod: ko.observable(),
        goToResume: ko.observable(),
        isShippingMethodError: ko.observable(),
        updateOptions: ko.observable(),

        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            this.setIsDisabledShippingStep();
            this.isShippingStepFinished.subscribe(function (value) {
                console.log("isShippingStepFinished:" + value);
                shippingPayment.isShippingStepFinished(value);
                shippingPayment.setShippingMethodModelData(quote);
                shippingPayment.setShippingModelData(quote);
                this.setIsDisabledShippingStep();
            },this);
            this.goToResume.subscribe(function (value) {
                //TODO Call here setIsDisabledShippingStep to update isShippingStepFinished
                console.log("hola");
            },this);
            customer.isCustomerStepFinished.subscribe(function (value) {
                if(value.includes('_complete')){
                    this.validateRates();
                }
            },this);
            return this;
        },
        /**
         * Overwrite set shipping information action
         */
        setShippingInformation: function () {
            this.setDataToShippingForm();
            if (this.validateShippingInformation()) {
                this.isShippingStepFinished("_complete");
                if (shippingPayment.shippingMethod() === 'instore'){
                    this.isShippingStepFinished.notifySubscribers("_complete");
                }
                this.goToResume(false);
            }else{
                this.isShippingStepFinished("_active");
                this.goToResume(true);
            }
            this._super();
        },
        /**
         * Update progress bar to complete or incomplete state
         * TODO validate isDisabledShippingStep because could be _complete, _active and empty
         * but empty its not implemented yet
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
            if(t.currentTarget && !this.isRegularShippingDisabled()){
                this.isRegularShipping(true);
                this.isFastShipping(false);
                var input = t.currentTarget.querySelector('input[id="envio_regular"]');
                if (input) {
                    input.checked = true;
                }
                this.setDataToShippingForm();
                var rate = this.findRateByCarrierCode('flatrate');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
            }
        },
        /**
         * Set shipping method for fast shipping
         * @param e
         * @param t
         */
        setFastShipping: function (e,t) {
            if(t.currentTarget && !this.isFastShippingDisabled()){
                this.isRegularShipping(false);
                this.isFastShipping(true);
                var input = t.currentTarget.querySelector('input[id="envio_rapido"]');
                if (input) {
                    input.checked = true;
                }
                var rate = this.findRateByCarrierCode('envio_rapido');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
            }
            if(t.currentTarget && this.isFastShippingDisabled()){
                this.isRegularShipping(false);
                this.isFastShipping(false);
                var input = t.currentTarget.querySelector('input[id="envio_regular"]');
                if (input) {
                    input.checked = false;
                }
                var rate = this.findRateByCarrierCode('envio_rapido');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
            }
        },
        /**
         * Show error when shipping method envio_rapido it's not available
         * @param rate
         * @returns {*}
         */
        showShippingMethodError: function (rate) {
            return this.isShippingMethodError(!!rate.error_message);
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
            var uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.firstname.value(customer.customerName());
            uiComponent.lastname.value(customer.customerLastName());
            uiComponent.telephone.value(customer.customerTelephone());
            uiComponentsRequired = ["vat_id","distrito_envio_rapido","invoice_required","company","dni"];
            uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.vat_id.validation = Object.assign({}, uiComponent.vat_id.validation, newValidationConfig);
            uiComponent.distrito_envio_rapido.validation = Object.assign({}, uiComponent.distrito_envio_rapido.validation, newValidationConfig);
            uiComponent.invoice_required.validation = Object.assign({}, uiComponent.invoice_required.validation, newValidationConfig);
            uiComponent.company.validation = Object.assign({}, uiComponent.company.validation, newValidationConfig);
            uiComponent.dni.validation = Object.assign({}, uiComponent.company.dni, newValidationConfig);
        },
        /**
         * get text value of carrier amount by method type
         * @param methodType
         * @returns {*}
         */
        getPriceLabel: function (methodType){
            var carrier;
            var selectedShippingRate = checkoutData.getSelectedShippingRate();
            if (methodType === "selected") {
                if (!_.isUndefined(selectedShippingRate) &&
                    !_.isNull(selectedShippingRate) ) {
                    methodType = selectedShippingRate;
                } else {
                    return "No ha seleccionado un precio aun.";
                }
            }
            carrier = this.getCarrierCodeByCarrier(methodType);
            return priceUtils.formatPrice(carrier.amount);
        },
        /**
         * Get carrier by string that contains carrierCode
         * @param carrierCode
         * @returns {*|boolean}
         */
        getCarrierCodeByCarrier: function (carrierCode) {
            if (_.isEmpty(this.rates())){
                return false;
            }
            var carrier = _.find(this.rates(),function(rate) {
                return carrierCode.includes(rate.carrier_code);
            });
            if (carrier){
                return carrier;
            }
            return false;
        },
        /**
         * validate if shippingMethod is Available
         * @param methodType
         * @returns {boolean}
         */
        isShippingMethodAvailable: function (methodType) {
            var carrier = this.getCarrierCodeByCarrier(methodType);
            //console.log("Tiene error: " + !!carrier.error_message + " El carrier: " + carrier.carrier_code);
            if(_.isObject(carrier)){
                if(carrier.carrier_code.includes('rapid'))
                this.updateOptions(carrier.extension_attributes.delivery_time);
            }
            if(!!carrier.error_message && carrier.carrier_code.includes('flat')){
                this.isRegularShippingDisabled(true);
            }else{
                this.isRegularShippingDisabled(false);
            }
            if (!!carrier.error_message && carrier.carrier_code.includes('rapid')){
                this.isFastShippingDisabled(true);
            }else{
                this.isFastShippingDisabled(false);
            }
            return !!carrier.error_message;
        },
        /**
         * validates if some rates have error to disabled
         */
        validateRates: function () {
            var carrierWithErrorMessage = _.find(this.rates(), function(rate) {
                if(!!rate.error_message){
                    return rate;
                }
            });
            if(carrierWithErrorMessage){
                this.isShippingMethodAvailable(carrierWithErrorMessage.carrier_code);
            }
        }
    }

    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }
});
