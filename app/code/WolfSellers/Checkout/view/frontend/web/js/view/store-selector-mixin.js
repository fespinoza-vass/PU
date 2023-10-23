define([
    'ko',
    'underscore',
    'uiRegistry',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service'
], function (
    ko,
    _,
    registry,
    wolfUtils,
    quote,
    shippingPayment,
    customer,
    pickupLocationsService
) {
    'use strict';

    var storeSeletorMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/store-selector',
            storeSelectorPopupTemplate:
                'WolfSellers_Checkout/store-selector/contentSearch',
            storeSelectorPopupItemTemplate:
                'WolfSellers_Checkout/store-selector/popup-item',
            links: {
                "goToResume":'checkout:isVisibleShipping',
                "isAnotherPicker":'checkout.steps.store-pickup.store-selector.picker.pickerOption:value'
            },
            defaultCountryId: "PE"
        },
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),
        goToResume:ko.observable(),
        isAnotherPicker:ko.observable(),

        initialize: function () {
            this._super();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                shippingPayment.setShippingMethodModelData(quote);
                shippingPayment.setPickupModelData(this.selectedLocation());
                this.setIsDisabledShippingStep();
            },this);
            this.goToResume.subscribe(function (value) {
                if (!value){
                    shippingPayment.isStepTwoFinished('_active');
                    shippingPayment.isShippingStepFinished('_complete');
                    this.setIsDisabledShippingStep();
                }else{
                    shippingPayment.isStepTwoFinished('_active');
                    shippingPayment.isShippingStepFinished('_active');
                    this.setIsDisabledShippingStep();
                }
            },this);
        },
        /**
         * Overwrite set pickup information action
         */
        setPickupInformation: function () {
            if (customer.isCustomerStepFinished() === '_complete') {
                if (this.validatePickupInformation()) {
                    this.isShippingStepFinished.notifySubscribers("_complete");
                    this.goToResume(false);
                } else {
                    this.isShippingStepFinished("_active");
                    this.goToResume(true);
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
                this.isDisabledShippingStep(true);
            }else{
                this.isDisabledShippingStep(false);
            }
        },
        /**
         * overwrite original function because there isn't a form to validate.
         * @returns {boolean}
         */
        validatePickupInformation: function () {
            if(this.isAnotherPickerAreaVisible()){
                var anotherPickerForm = registry.get("checkout.steps.store-pickup.store-selector.another-picker");
                if(!anotherPickerForm.validateAnotherPickerForm()){
                    return false;
                }
            }
            var voucher = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.voucher");
            if(_.isUndefined(voucher.value())){
                voucher.error('Este es un campo obligatorio.');
                return false;
            }else{
                voucher.error('');
            }
            var direccion_picker_voucher = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.direccion_comprobante_picker");
            if(!direccion_picker_voucher.value()){
                direccion_picker_voucher.error('Este es un campo obligatorio.');
                return false;
            }else{
                direccion_picker_voucher.error('');
            }
            //TODO validar los inputs que no son de pick up cómo no requeridos
            return true;
        },
        /**
         * Unselect a location store selected
         */
        unSelectLocation: function () {
            this.selectedLocation(null);
        },
        /**
         * Overrides original selectedPickUpLocatio to avoid modal options
         * @param location
         */
        selectPickupLocation: function (location) {
            pickupLocationsService.selectForShipping(location);
        },
        /**
         * validate if is another Picker Area visible
         * @returns {boolean|*}
         */
        isAnotherPickerAreaVisible: function () {
            var isAnotherPicker = this.isAnotherPicker();
            return (_.isString(isAnotherPicker) &&
                isAnotherPicker.includes("other"));
        },
        /**
         * generate ID for input and label
         * @param data
         * @returns {string}
         */
        getId: function (data) {
            var nombre = data.name.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            var region = data.region.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            var nombreUltimos = nombre.slice(-4);
            var resultado = nombreUltimos + "_" + region;
            return resultado;
        },
        /**
         * Update nerby locations
         * @param searchQuery
         * @returns {*}
         */
        updateNearbyLocations: function (searchQuery) {
            if (_.isUndefined(searchQuery)){
                return this._super(searchQuery);
            }
            console.log(searchQuery);
            searchQuery = searchQuery.replace(':US', ':PE');
            console.log(searchQuery);
            return this._super(searchQuery);
        },
        /**
         * Visible span input
         * @returns {boolean}
         */
        isActiveFade: function(){
            var distrito = registry.get('checkout.steps.store-pickup.store-selector.distrito-pickup.distrito').value();

            if(distrito){
                return false;
            }else{
                return true;
            }
        },
        /**
         * Visible store options
         * @param value
         * @returns {boolean}
         */
        activeOptions: function (value){
            var distrito = registry.get('checkout.steps.store-pickup.store-selector.distrito-pickup.distrito').value();

            if(distrito){
                return true;
            }else{
                return false;
            }
        },
        /**
         * Shipping Pickup Date Format
         */
        getPickupDateFormat: function (){
            var date = "";
            var ahora = new Date();
            var fechaEntrega = wolfUtils.formatDate(ahora);
            date = fechaEntrega;
            return date;
        }

    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});
