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
    var regularAddressPath = "checkout.steps.shipping-step.shippingAddress.regular.";
    var fastAddressPath = "checkout.steps.shipping-step.shippingAddress.fast.";
    var voucherPath = 'checkout.steps.store-pickup.store-selector.picker-voucher.';
    var pickerPath = "checkout.steps.store-pickup.store-selector.picker.";
    var anotherPicker = "checkout.steps.store-pickup.store-selector.another-picker.";

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
        isContinueBtnDisabled: ko.observable(true),

        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            this.setIsDisabledShippingStep();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                shippingPayment.setShippingMethodModelData(quote);
                shippingPayment.setShippingModelData(quote);
                this.setIsDisabledShippingStep();
            },this);
            this.goToResume.subscribe(function (value) {
                if (!value){
                    shippingPayment.isStepTwoFinished('_active');
                    shippingPayment.isShippingStepFinished('_complete');
                    this.isShippingStepFinished('_complete');
                    this.setIsDisabledShippingStep();
                }else{
                    shippingPayment.isShippingStepFinished('_active');
                    this.isShippingStepFinished('_active');
                    shippingPayment.isStepTwoFinished('_active');
                    this.setIsDisabledShippingStep();
                }
            },this);
            customer.isCustomerStepFinished.subscribe(function (value) {
                this.isContinueBtnDisabled(true);
                if(value.includes('_complete')){
                    this.validateRates();
                    this.isContinueBtnDisabled(false);
                }
            },this);
            return this;
        },
        /**
         * Overwrite set shipping information action
         */
        setShippingInformation: function () {
            if (!this.isFastShipping() && !this.isRegularShipping()){
                if(!customer.isCustomerLoggedIn){
                    quote.shippingMethod(null);
                }
                this.errorValidationMessage(
                    $t('The shipping method is missing. Select the shipping method and try again.')
                );
                return false;
            }
            if (customer.isCustomerStepFinished() === '_complete') {
                this.source.set('params.invalid', false);
                this.triggerShippingDataValidateEvent();
                if (!this.source.get('params.invalid')) {
                    this.setDataToShippingForm();
                    if (this.validateShippingInformation()) {
                        this.isShippingStepFinished("_complete");
                        if (shippingPayment.shippingMethod() === 'instore') {
                            this.isShippingStepFinished.notifySubscribers("_complete");
                        }
                        this.goToResume(false);
                    } else {
                        this.isShippingStepFinished("_active");
                        this.goToResume(true);
                    }
                }else{
                    return false;
                }
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
                this.isDisabledShippingStep(false);
            }else{
                this.isDisabledShippingStep(true);
            }
        },
        /**
         * Set shipping method flatRate for regular shipping
         */
        setRegularShipping: function () {
            if(!this.isRegularShippingDisabled()){
                var departamentoRegular = registry.get("checkout.steps.shipping-step.shippingAddress.regular.departamento");
                departamentoRegular.reset();
                this.isRegularShipping(true);
                this.isFastShipping(false);
                var rate = this.findRateByCarrierCode('flatrate');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
                this.updateShippingValidations();
            }
        },
        /**
         * Set shipping method for fast shipping
         */
        setFastShipping: function () {
            if(!this.isFastShippingDisabled()){
                var street = registry.get("checkout.steps.shipping-step.shippingAddress.fast.direccion.0");
                street.reset();
                this.isRegularShipping(false);
                this.isFastShipping(true);
                var rate = this.findRateByCarrierCode('envio_rapido');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
                this.updateShippingValidations();
            }
            if(this.isFastShippingDisabled()){
                this.isRegularShipping(false);
                this.isFastShipping(false);
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
            var uiComponentsRequired = [];
            var uiComponent;
            var allShippingPickupComponents = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio",
                "distrito_envio_rapido",
                "horarios_disponibles",
                "picker",
                "identificacion_picker",
                "distrito_pickup",
                "numero_identificacion_picker",
                "nombre_completo_picker",
                "email_picker",
                "company",
                "postcode",
                "country_id"
            ];
            var customerDataInputs = [
                "firstname",
                "lastname",
                "telephone",
                "vat_id"
            ];
            var regular = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var regularComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "provincia",
                "departamento",
                "distrito"
            ]
            var fastComponentsArea = [
                'distrito',
                'direccion',
                'referencia'
            ];
            var rapido = [
                "distrito_envio_rapido",
                "street.0",
                "referencia_envio",
                "horarios_disponibles"
            ];
            var pickerVoucherPath = [
                "voucher",
                "direccion_comprobante_picker"
            ];
            var pickerPickerPath = [
                "pickerOption"
            ];
            var pickerAnotherPicker = [
                "identificacion_picker",
                "numero_identificacion_picker",
                "nombre_completo_picker",
                "email_picker"
            ];
            /**
             * Customer Data
             */
            uiComponentsRequired = customerDataInputs;
            uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.firstname.value(customer.customerName());
            uiComponent.lastname.value(customer.customerLastName());
            uiComponent.telephone.value(customer.customerTelephone());
            uiComponent.vat_id.value(customer.customerNumberIdentification());
            wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, allShippingPickupComponents, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(voucherPath, pickerVoucherPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(pickerPath, pickerPickerPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(anotherPicker, pickerAnotherPicker, newValidationConfig);
            /**
             * Envio Regular
             */
            if(this.isRegularShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, regular, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
                uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, regular);

                uiComponent.region_id.value();
                uiComponent.city.value();
                uiComponent.colony.value();
                uiComponent['street.0'].value();
                uiComponent.referencia_envio.value();
            }
            /**
             * Envio Rápido
             */
            if (this.isFastShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, rapido, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
                var horarios_disponibles = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.horarios_disponibles");
                var distrito_envio_rapido = registry.get("checkout.steps.shipping-step.shippingAddress.fast.distrito");
                var direccion = registry.get("checkout.steps.shipping-step.shippingAddress.fast.direccion.0");
                var region = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id");
                var referenciaRapida = registry.get("checkout.steps.shipping-step.shippingAddress.fast.referencia");
                var regionId = distrito_envio_rapido.getOption(distrito_envio_rapido.value());
                horarios_disponibles.validation = [{'required-entry':false}];
                uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, rapido);

                uiComponent.distrito_envio_rapido.options(distrito_envio_rapido.options());
                uiComponent.distrito_envio_rapido.value(distrito_envio_rapido.value());
                uiComponent['street.0'].value(direccion.value());
                uiComponent.referencia_envio.value(referenciaRapida.value());
                referenciaRapida.validate();
                if(referenciaRapida.error()){
                    uiComponent.referencia_envio.validate();
                    referenciaRapida.error(referenciaRapida.error());
                    return;
                }


                region.value(regionId.region_id);
            }
            var facturacion = [
                "dni",
                "invoice_required",
                "ruc",
                "razon_social"
            ];

        },
        /**
         * Set validation for each input according to shipping Method
         */
        updateShippingValidations: function () {
            var validationRuleName = 'required-entry';
            var newValidationConfig = {
                [validationRuleName]: false
            };
            var allShippingPickupComponents = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio",
                "distrito_envio_rapido",
                "horarios_disponibles",
                "picker",
                "identificacion_picker",
                "distrito_pickup",
                "numero_identificacion_picker",
                "nombre_completo_picker",
                "email_picker",
                "company",
                "postcode",
                "country_id"
            ];
            var regular = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var regularComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "provincia",
                "departamento",
                "distrito"
            ]
            var fastComponentsArea = [
                'distrito',
                'direccion',
                'referencia'
            ];
            var pickerVoucherPath = [
                "voucher",
                "direccion_comprobante_picker"
            ];
            var pickerPickerPath = [
                "pickerOption"
            ];
            var pickerAnotherPicker = [
                "identificacion_picker",
                "numero_identificacion_picker",
                "nombre_completo_picker",
                "email_picker"
            ];
            var customerDataInputs = [
                "firstname",
                "lastname",
                "telephone",
                "vat_id"
            ];
            var uiComponentsRequired = [];
            var uiComponent;
            uiComponentsRequired = customerDataInputs;
            uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, uiComponentsRequired);
            uiComponent.firstname.value(customer.customerName());
            uiComponent.lastname.value(customer.customerLastName());
            uiComponent.telephone.value(customer.customerTelephone());
            uiComponent.vat_id.value(customer.customerNumberIdentification());
            wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, allShippingPickupComponents, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(voucherPath, pickerVoucherPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(pickerPath, pickerPickerPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(anotherPicker, pickerAnotherPicker, newValidationConfig);
            if(this.isRegularShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, regular, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
            }
            if(this.isFastShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
            }

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
