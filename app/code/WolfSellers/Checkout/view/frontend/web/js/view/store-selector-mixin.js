define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer',
    'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service',
], function (
    $,
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
        shippingAddressAtPickup: ko.observable(),
        isAvailable: ko.observable(false),

        initialize: function () {
            var self = this;
            self._super();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                shippingPayment.setShippingMethodModelData(quote);
                shippingPayment.setPickupModelData(this.selectedLocation(),quote);
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

            self.messageBasedOnAvailability = ko.computed(function() {
                return self.isAvailable() ?
                    'Recuerda que tienes hasta 7 días posteriores a tu compra para recoger tus productos, de lo contrario tu compra será anulada y se procederá a la devolución del dinero.' :
                    'Podrás recoger tu pedido en un lapso de 24 a 48 horas.';
            });
        },
        /**
         * Overwrite set pickup information action
         */
        setPickupInformation: function () {
            if (customer.isCustomerStepFinished() === '_complete') {
                if (this.validatePickupInformation()) {
                    this.isShippingStepFinished.notifySubscribers("_complete");
                    quote.shippingAddress(this.shippingAddressAtPickup());
                    quote.billingAddress(quote.shippingAddress());
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
            var picker = registry.get("checkout.steps.store-pickup.store-selector.picker.pickerOption");
            if(_.isUndefined(picker.value())){
                picker.error('Este es un campo obligatorio.');
                return false;
            }else{
                picker.error('');
            }
            if(this.isAnotherPickerAreaVisible()){
                var anotherPickerForm = registry.get("checkout.steps.store-pickup.store-selector.another-picker");
                if(!anotherPickerForm.validateAnotherPickerForm()){
                    return false;
                }
            }
            var voucher = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.voucher");
            if(_.isUndefined(voucher.value())){
                /********* Currently this information is not used ***********/
                //voucher.error('Este es un campo obligatorio.');
                //return true;
            }else{
                voucher.error('');
            }
            var direccion_picker_voucher = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.direccion_comprobante_picker");
            if(!direccion_picker_voucher.value()){
                /********* Currently this information is not used ***********/
                //direccion_picker_voucher.error('Este es un campo obligatorio.');
                //return true;
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
         * Sets the pickup location and fetches fast delivery availability.
         * @param {Object} location - The location object representing the pickup point.
         */
        selectPickupLocation(location) {
            pickupLocationsService.selectForShipping(location);
            this.shippingAddressAtPickup(quote.shippingAddress());
            this.getLurinDeliveryData();
        },

        /**
         * Fetches lurin delivery availability data from the server and updates availability.
         */
        getLurinDeliveryData() {
            const lurinDeliveryUrl = `${BASE_URL}/bopis/ajax/islurinonly`;

            $.ajax({
                url: lurinDeliveryUrl,
                type: 'GET',
                dataType: 'json',
                context: this,
                success: function(response) {
                    this.isAvailable(!(response && (response.available === '1')));
                    shippingPayment.horarioTienda(response.available);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(`AJAX request failed: ${textStatus}, ${errorThrown}`);
                    this.isAvailable(false);
                }
            });
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
            var code = data.pickup_location_code;
            var nombreUltimos = nombre.slice(-4);
            var resultado = nombreUltimos + "_" + region + "_" + code;

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
            searchQuery = searchQuery.replace(':US', ':PE');
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
         * Returns the pickup date based on availability and store timing.
         * @return {string} Formatted date.
         */
        getPickupDateFormat: function() {
            var isFastDeliveryAvailable = shippingPayment.horarioTienda() === '1';

            var now = new Date();

            if (isFastDeliveryAvailable) {
                now.setDate(now.getDate() + 2);
            }

            var formattedDate = wolfUtils.formatDate(now);

            return formattedDate;
        },

        /**
         * Validate products labels match
         * @returns {boolean}
         */
        isLabelsMatch: function (){
            var rules = window.checkoutConfig.ruleslabelsApplied;

            if(rules.fastShipping == true && rules.inStorePickup == true && rules.noRules == true){
                return false;
            }
            return true;
        }
    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});

