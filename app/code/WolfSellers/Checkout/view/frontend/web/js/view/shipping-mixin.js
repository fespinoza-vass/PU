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
    'WolfSellers_Checkout/js/view/shipping-additional-info-modal',
    'WolfSellers_Checkout/js/model/step-summary',
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
    checkoutData,
    additionalInfoModal,
    stepSummary
) {
    'use strict';
    var shippingAddressPath = "checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.";
    var urbanoAddressPath = "checkout.steps.shipping-step.shippingAddress.urbano.";
    var regularAddressPath = "checkout.steps.shipping-step.shippingAddress.regular.";
    var fastAddressPath = "checkout.steps.shipping-step.shippingAddress.fast.";
    var voucherPath = 'checkout.steps.store-pickup.store-selector.picker-voucher.';
    var pickerPath = "checkout.steps.store-pickup.store-selector.picker.";
    var anotherPicker = "checkout.steps.store-pickup.store-selector.another-picker.";
    var checkoutConfig = window.checkoutConfig;

    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
            shippingMethods: "WolfSellers_Checkout/shippingMethod",
            links: {
                "goToResume":'checkout:isVisibleShipping',
                "updateOptions":"checkout.steps.shipping-step.shippingAddress.schedule.schedule:updateOptions",
            },
            imports: {
                "ubigeo":"checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.postcode:value",
                "isStorePickUpSelected": "checkout.steps.store-pickup:isStorePickupSelected",
                "currentDistrito": "checkout.steps.shipping-step.shippingAddress.urbano.distrito:value"
            }
        },
        isFormInline: true,
        isActive: ko.observable(false),
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),
        isRegularShipping: ko.observable(false),
        isUrbanoShipping: ko.observable(false),
        isFastShipping: ko.observable(false),
        isFastShippingDisabled: ko.observable(false),
        isRegularShippingDisabled: ko.observable(false),
        isUrbanoShippingDisabled: ko.observable(false),
        shippingMethod: ko.observable(),
        goToResume: ko.observable(),
        isShippingMethodError: ko.observable(),
        updateOptions: ko.observable(),
        isContinueBtnDisabled: ko.observable(true),
        ubigeo: ko.observable(''),
        errorMessage: ko.observable(''),
        isUrbanoMethodConfigured: ko.observable(true),
        isRegularMethodConfigured: ko.observable(true),
        isFastMethodConfigured: ko.observable(true),
        isStorePickUpSelected: ko.observable(false),
        isDebuggEnable: ko.observable(false),
        currentDistrito: ko.observable(false),
        shippingTimeMessage: ko.observable("<br>"),
        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            var activeCarriers = checkoutConfig.activeCarriers;
            this.isUrbanoMethodConfigured(_.includes(activeCarriers, 'urbano'));
            this.isRegularMethodConfigured(_.includes(activeCarriers, 'flatrate'));
            this.isFastMethodConfigured(_.includes(activeCarriers, 'envio_rapido'));

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
                    if(shippingPayment.isPaymentStepFinished() === '_complete'){
                        stepSummary.isStepTreeFinished('_active');
                    } else {
                        stepSummary.isStepTreeFinished('');
                    }
                    this.isShippingStepFinished('_complete');
                    this.setIsDisabledShippingStep();
                }else{
                    shippingPayment.isShippingStepFinished('_active');
                    this.isShippingStepFinished('_active');
                    shippingPayment.isStepTwoFinished('_active');
                    stepSummary.isStepTreeFinished('');

                    this.setIsDisabledShippingStep();
                }
            },this);
            customer.isCustomerStepFinished.subscribe(function (value) {
                this.isContinueBtnDisabled(true);
                if(value.includes('_complete')){
                    this.validateRates();
                    this.isContinueBtnDisabled(false);
                    shippingPayment.isStepTwoFinished('_active');
                }
            },this);
            //Set this when 'til estimate shipping rates gets urbano
            this.ubigeo.subscribe(function (value) {
                if (!_.isUndefined(value) && this.isUrbanoShipping()){
                    var rate = this.findRateByCarrierCode('urbano');
                    if (!_.isUndefined(rate)){
                        this.showShippingMethodError(rate);
                        this.selectShippingMethod(rate);
                    }
                }
            },this);

            this.rates.subscribe(function (value) {
                if(!_.isUndefined(value) && !this.isStorePickUpSelected()){
                    if (!_.isUndefined(value) && this.isUrbanoShipping()){
                        var rate = this.findRateByCarrierCode('urbano');
                        if (!_.isUndefined(rate)){
                            this.showShippingMethodError(rate);
                            this.selectShippingMethod(rate);
                        }
                    }
                    if (!_.isUndefined(value) && this.isRegularShipping()){
                        var rate = this.findRateByCarrierCode('flatrate');
                        if (!_.isUndefined(rate)){
                            this.showShippingMethodError(rate);
                            this.selectShippingMethod(rate);
                        }
                    }
                    if (!_.isUndefined(value) && this.isFastShipping()){
                        var rate = this.findRateByCarrierCode('envio_rapido');
                        if (!_.isUndefined(rate)){
                            this.showShippingMethodError(rate);
                            this.selectShippingMethod(rate);
                        }
                    }
                }
                if(this.isDebuggEnable())console.log(value);
            },this);
            this.currentDistrito.subscribe(function (value) {
                if (!_.isUndefined(value)){
                    this.updateShippingTimeMessage(value);
                }
            },this);
            this.createInformationModals();
            return this;
        },
        /**
         * Overwrite set shipping information action
         */
        setShippingInformation: function () {
            if (!this.isFastShipping() && !this.isRegularShipping() && !this.isUrbanoShipping()){
                if(!customer.isCustomerLoggedIn){
                    quote.shippingMethod(null);
                }
                this.errorValidationMessage(
                    $t('The shipping method is missing. Select the shipping method and try again.')
                );
                return false;
            }
            var rate = this.findRateByCarrierCode('freeshipping');
            if(rate !== undefined) {
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
            } else {
                var rate = this.findRateByCarrierCode('urbano');
                if (rate !== undefined) {
                    this.showShippingMethodError(rate);
                    this.selectShippingMethod(rate);
                }
            }
            if (customer.isCustomerStepFinished() === '_complete') {
                this.source.set('params.invalid', false);
                this.triggerShippingDataValidateEvent();
                this.validateShippingInformation();
                if (!this.source.get('params.invalid')) {
                    if (!this.setDataToShippingForm()) {
                        return false;
                    }
                    if (this.validateShippingInformation()) {
                        this.isShippingStepFinished("_complete");
                        if (shippingPayment.shippingMethod() === 'instore') {
                            this.isShippingStepFinished.notifySubscribers("_complete");
                        }
                        this.goToResume(false);
                        var visanet = registry.get("checkout.steps.billing-step.payment.payments-list.visanet_pay");
                        if(!_.isUndefined(visanet)){
                            visanet.selectPaymentMethod();
                        }
                    } else {
                        this.isShippingStepFinished("_active");
                        this.goToResume(true);
                    }
                } else {
                    return false;
                }
                setTimeout(function () {
                    $('html, body').animate({scrollTop: ($("#payment").offset().top - 50)}, 1000);
                }, 500);
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
                this.isUrbanoShipping(false);
                var rate = this.findRateByCarrierCode('flatrate');
                this.showShippingMethodError(rate);
                this.selectShippingMethod(rate);
                this.updateShippingValidations();
                this.isShippingMethodError(false);
                return true;
            }
            this.isShippingMethodError(true);
            this.errorMessage('');
            this.errorMessage('Tu pedido no puede ser procesado por Envío Regular.');
            setTimeout(function () {
                shippingMixin.isShippingMethodError(false);
            }, 4000);
            return false
        },
        /**
         * Set shipping method urbano for regular shipping
         */
        setUrbanoShipping: function () {
            if(!this.isUrbanoShippingDisabled()){
                var departamentoRegular = registry.get("checkout.steps.shipping-step.shippingAddress.regular.departamento");
                departamentoRegular.reset();
                this.isRegularShipping(false);
                this.isFastShipping(false);
                this.isUrbanoShipping(true);
                this.updateShippingValidations();
                this.isShippingMethodError(false);
                return true;
            }
            this.isShippingMethodError(true);
            this.errorMessage('');
            this.errorMessage('Tu pedido no puede ser procesado por Envío Regular.');
            setTimeout(function () {
                shippingMixin.isShippingMethodError(false);
            }, 4000);
            return false
        },
        /**
         * Set shipping method for fast shipping
         */
        setFastShipping: function () {
            if(!this.isFastShippingDisabled()){
                var rate = this.findRateByCarrierCode('envio_rapido');
                if(!this.showShippingMethodError(rate)){

                    return false
                }
                var street = registry.get("checkout.steps.shipping-step.shippingAddress.fast.direccion.0");
                street.reset();
                var schedule = registry.get('checkout.steps.shipping-step.shippingAddress.schedule.schedule');
                schedule.reset();
                this.isRegularShipping(false);
                this.isUrbanoShipping(false);
                this.isFastShipping(true);
                this.selectShippingMethod(rate);
                this.updateShippingValidations();
                return true;
            }
            this.isShippingMethodError(true);
            this.errorMessage('');
            this.errorMessage('Tu pedido no puede ser procesado por Envío rápido.');
            setTimeout(function () {
                shippingMixin.isShippingMethodError(false);
            }, 4000);
            return false
        },
        /**
         * Show error when shipping method envio_rapido it's not available
         * @param rate
         * @returns {*}
         */
        showShippingMethodError: function (rate) {
            if(_.isUndefined(rate)){
                return false;
            }
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
            if(methodType === 'urbano' && carrier.amount === 0){
                return 'S/0.00';
            }
            if (!carrier && methodType === "urbano" && this.isUrbanoShipping()){
                return "Calculando...";
            }
            if (!carrier && methodType === "urbano" &&
                !this.isUrbanoShipping() && !this.isFastShipping() && !this.isRegularShipping()){
                return "Calculando...";
            }
            return priceUtils.formatPrice(carrier.amount);
        },
        /**
         * Get carrier by string that contains carrierCode
         * @param carrierCode
         * @returns {*|boolean}
         */
        getCarrierCodeByCarrier: function (carrierCode) {
            if (_.isEmpty(this.rates()) || carrierCode === undefined){
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
            if (carrier){
                if(!_.isObject(carrier)){
                    return false;
                }
                if(!!carrier.error_message && carrier.carrier_code.includes('urbano')){
                    this.isUrbanoShippingDisabled(true);
                    return true;
                }
                if(!!carrier.error_message && carrier.carrier_code.includes('flat')){
                    this.isRegularShippingDisabled(true);
                }else{
                    this.isRegularShippingDisabled(false);
                }
                if (!!carrier.error_message && carrier.carrier_code.includes('rapid')){
                    this.isFastShippingDisabled(true);
                }else{
                    if (carrier.carrier_code.includes('rapid')){
                        this.updateOptions(carrier.extension_attributes.delivery_time);
                    }
                    this.isFastShippingDisabled(false);
                }
                return !!carrier.error_message;
            }
            return false;
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
        },
        /**
         * Create Modals
         */
        createInformationModals: function () {
            if (additionalInfoModal.modalContentForRegularDelivery == null) {
                additionalInfoModal.createModalRS('#regularshipping-additional-info-modal');
            }
            if (additionalInfoModal.modalContentForFastDelivery == null) {
                additionalInfoModal.createModalFS('#fastshipping-additional-info-modal');
            }
            return true;
        },
        /**
         * Show Additional Modal Information for Regular Shipping
         */
        showRSInformation: function () {
            additionalInfoModal.showModalRSInformation();
        },
        /**
         * Show Additional Modal Information for Fast Shipping
         */
        showFSInformation: function () {
            additionalInfoModal.showModalFSInformation();
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
            var urbano = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var regular = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var urbanoComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "referencia"
            ]
            var regularComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "referencia"
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
            var referenciaRegular = {};
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
            wolfUtils.setUiComponentsArrayValidation(urbanoAddressPath, urbanoComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(voucherPath, pickerVoucherPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(pickerPath, pickerPickerPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(anotherPicker, pickerAnotherPicker, newValidationConfig);
            /**
             * Envio Urbano
             */
            if(this.isUrbanoShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, regular, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(urbanoAddressPath, urbanoComponentsArea, newValidationConfig);
                uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, urbano);
                referenciaRegular = registry.get("checkout.steps.shipping-step.shippingAddress.urbano.referencia");
                uiComponent.region_id.value();
                uiComponent.city.value();
                uiComponent.colony.value();
                uiComponent['street.0'].value();
                uiComponent.referencia_envio.value(referenciaRegular.value());
            }
            /**
             * Envio Regular
             */
            if(this.isRegularShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, regular, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
                uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, regular);
                referenciaRegular = registry.get("checkout.steps.shipping-step.shippingAddress.regular.referencia")
                uiComponent.region_id.value();
                uiComponent.city.value();
                uiComponent.colony.value();
                uiComponent['street.0'].value();
                uiComponent.referencia_envio.value(referenciaRegular.value());
            }
            /**
             * Envio Rápido
             */
            if (this.isFastShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, rapido, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
                var horarios_disponiblesFast = registry.get("checkout.steps.shipping-step.shippingAddress.schedule.schedule");
                var distrito_envio_rapido = registry.get("checkout.steps.shipping-step.shippingAddress.fast.distrito");
                var direccion = registry.get("checkout.steps.shipping-step.shippingAddress.fast.direccion.0");
                var region = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id");
                var referenciaRapida = registry.get("checkout.steps.shipping-step.shippingAddress.fast.referencia");
                var regionId = distrito_envio_rapido.getOption(distrito_envio_rapido.value());
                uiComponent = wolfUtils.getUiComponentsArray(shippingAddressPath, rapido);

                uiComponent.horarios_disponibles.validation = [{'required-entry':false}];
                uiComponent.distrito_envio_rapido.options(distrito_envio_rapido.options());
                var valueDistrito = _.findIndex(distrito_envio_rapido.options(), {value:distrito_envio_rapido.value()});
                valueDistrito = distrito_envio_rapido.options()[valueDistrito].labeltitle;
                uiComponent.distrito_envio_rapido.value(valueDistrito);
                uiComponent['street.0'].value(direccion.value());
                uiComponent.referencia_envio.value(referenciaRapida.value());
                if(_.isUndefined(horarios_disponiblesFast.value())){
                    horarios_disponiblesFast.error("Seleccione un horario de entrega");
                    return false;
                }
                referenciaRapida.validate();
                if(referenciaRapida.error()){
                    uiComponent.referencia_envio.validate();
                    referenciaRapida.error(referenciaRapida.error());
                    return false;
                }
                region.value(regionId.region_id);
            }
            return true;
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
            var urbano = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var regular = [
                "region_id",
                "city",
                "colony",
                "street.0",
                "referencia_envio"
            ];
            var urbanoComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "referencia"
            ]
            var regularComponentsArea = [
                "provincia",
                "departamento",
                "distrito",
                "referencia"
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
            wolfUtils.setUiComponentsArrayValidation(urbanoAddressPath, urbanoComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(regularAddressPath, regularComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(fastAddressPath, fastComponentsArea, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(voucherPath, pickerVoucherPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(pickerPath, pickerPickerPath, newValidationConfig);
            wolfUtils.setUiComponentsArrayValidation(anotherPicker, pickerAnotherPicker, newValidationConfig);

            if(this.isUrbanoShipping()){
                newValidationConfig['required-entry'] = true;
                wolfUtils.setUiComponentsArrayValidation(shippingAddressPath, urbano, newValidationConfig);
                wolfUtils.setUiComponentsArrayValidation(urbanoAddressPath, urbanoComponentsArea, newValidationConfig);
            }
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
         *
         */
        updateShippingTimeMessage: function (currentDistrict) {
            var shippingSettings = window.checkoutConfig.shippingSettings || {};
            var selectedDistrict = currentDistrict;

            var openingConfig;

            if (shippingSettings.openings_1 && shippingSettings.openings_1.configuredLocations.toUpperCase().split(',').includes(selectedDistrict.toUpperCase())) {
                openingConfig = shippingSettings.openings_1;
            } else if (shippingSettings.openings_2 && shippingSettings.openings_2.configuredLocations.toUpperCase().split(',').includes(selectedDistrict.toUpperCase())) {
                openingConfig = shippingSettings.openings_2;
            } else {
                openingConfig = shippingSettings.openings_1 || {};
            }

            this.shippingTimeMessage(openingConfig.deliveryTimeMessage || $t('Información de entrega no disponible'));
        },

    }

    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }
});
