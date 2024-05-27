define([
    'ko',
    'underscore',
    'uiComponent',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents'
], function (
    ko,
    _,
    Component,
    shippingPayment,
    wolfUtils
) {
    'use strict';

    return Component.extend({
        defaults:{
            template:'WolfSellers_Checkout/shipping-step-summary'
        },
        isUrbano: ko.observable(true),
        isShipping: ko.observable(false),
        isStorePickUp: ko.observable(false),
        isFastShipping: ko.observable(false),
        initialize:function () {
            this._super();
            shippingPayment.shippingMethod.subscribe(function (value) {
                if(value.includes('rapido')){
                    this.isUrbano(false);
                    this.isShipping(false);
                    this.isFastShipping(true);
                    this.isStorePickUp(false);
                }
                if (value.includes('urbano') || value.includes('free')){
                    this.isUrbano(true);
                    this.isShipping(false);
                    this.isFastShipping(false);
                    this.isStorePickUp(false);
                }
                if (value.includes('instore')){
                    this.isUrbano(false);
                    this.isShipping(false);
                    this.isFastShipping(false);
                    this.isStorePickUp(true);
                }
                if(value.includes('flat')){
                    this.isUrbano(false);
                    this.isShipping(true);
                    this.isFastShipping(false);
                    this.isStorePickUp(false);
                }
            },this);
           return this;
        },
        /**
         * get text from shipping method
         * @returns {string}
         */
        getShippingMethod:function () {
            if (this.isReadyToShowSummary()){
                if (
                    shippingPayment.shippingMethod().includes("flat") ||
                    shippingPayment.shippingMethod().includes("urban") ||
                    shippingPayment.shippingMethod().includes("free")
                ) {
                    return "Envío regular a domicilio";
                }
                if (shippingPayment.shippingMethod().includes("rapido")){
                    return "Envío rápido a domicilio";
                }
                if (shippingPayment.shippingMethod().includes("instore")){
                    return "Retiro en Tienda";
                }
            }
            return "Calculando...";
        },
        /**
         * Add text value for reference input
         * @returns {*|string}
         */
        getReferencia: function () {
            if (this.isReadyToShowSummary()) {
                return shippingPayment.referencia();
            }
            return "Calculando...";
        },
        /**
         * Add text value for store pickup selected input
         * @returns {string}
         */
        getStorePickUpSelected: function () {
            if (this.isReadyToShowSummary()) {
                return shippingPayment.tiendaSeleccionada();
            }

            return "Calculando...";
        },
        /**
         * Add text value for store pickup address selected
         * @returns {*|string}
         */
        getPickupAddress: function () {
            if (this.isReadyToShowSummary()){
                return shippingPayment.direccionTienda();
            }
            return "Calculando...";
        },
        /**
         * Add text value for shipping address
         * @returns {string}
         */
        getShippingAddress: function () {
            //Street + Distrito + departameto + provincia
            var distrito = shippingPayment.distrito();
            var departamento = shippingPayment.departamento();
            var provincia = shippingPayment.provincia();
            var split = " ";
            if (shippingPayment.shippingMethod().includes("rapido")){
                distrito = shippingPayment.distritoEnvioRapido();
            }
            if (shippingPayment.shippingMethod().includes("urban") || shippingPayment.shippingMethod().includes("free")){
                if(distrito.length >= 3){
                    distrito = distrito.charAt(0).toUpperCase() + distrito.slice(1).toLowerCase();
                }
                if(departamento.length >= 3){
                    departamento = departamento.charAt(0).toUpperCase() + departamento.slice(1).toLowerCase();
                }
                if(provincia.length >= 3){
                    provincia = provincia.charAt(0).toUpperCase() + provincia.slice(1).toLowerCase();
                }
                return shippingPayment.direccion() +split+ distrito +split+
                    departamento +split+ provincia;
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                if(distrito.length >= 3){
                    distrito = distrito.charAt(0).toUpperCase() + distrito.slice(1).toLowerCase();
                }
                return shippingPayment.direccion() +split+ distrito;
            }
            return shippingPayment.direccion() +split+ distrito +split+
                departamento +split+ provincia;
        },
        /**
         * get shipping Date
         * @returns {string}
         */
        getShippingDate: function () {
            var date = "";
            if (shippingPayment.shippingMethod().includes("urban") || shippingPayment.shippingMethod().includes("free")){
                date = "2 días naturales";
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                var horarioSeleccionado = shippingPayment.fechaEnvioRapido();
                date = horarioSeleccionado.fecha
            }
            if (shippingPayment.shippingMethod().includes("instore")){
                var rules = window.checkoutConfig.ruleslabelsApplied;
                if(!_.isUndefined(rules)){
                    if(rules.fastShipping == false && rules.inStorePickup == true && rules.noRules == false){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTienda();
                        date = horarioSeleccionado.fecha;
                    }

                    if(rules.fastShipping == true && rules.inStorePickup == true && rules.noRules == false){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTienda();
                        date = horarioSeleccionado.fecha;
                    }

                    if(rules.fastShipping == true && rules.inStorePickup == true && rules.noRules == true){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTiendaRule();
                        date = horarioSeleccionado.fecha;
                    }

                    if(rules.fastShipping == false && rules.inStorePickup == true && rules.noRules == true){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTiendaRule();
                        date = horarioSeleccionado.fecha;
                    }

                }else {
                    date = 'fecha de envío no disponible';
                }
            }

            return date;
        },
        /**
         * get shipping time for shipping summary
         * @returns {string}
         */
        getShippingTime:function () {
            var horario = "";
            if (shippingPayment.shippingMethod().includes("urban") || shippingPayment.shippingMethod().includes("free")){
                horario = "en un rango de 8 am a 7 pm";
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                var horarioSeleccionado = shippingPayment.fechaEnvioRapido();
                horario = horarioSeleccionado.horario;
            }
            if (shippingPayment.shippingMethod().includes("instore")){
                var rules = window.checkoutConfig.ruleslabelsApplied;
                if(!_.isUndefined(rules)){
                    if(rules.fastShipping == false && rules.inStorePickup == true && rules.noRules == false){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTienda();
                        horario = horarioSeleccionado.horario;
                    }

                    if(rules.fastShipping == true && rules.inStorePickup == true && rules.noRules == false){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTienda();
                        horario = horarioSeleccionado.horario;
                    }

                    if(rules.fastShipping == true && rules.inStorePickup == true && rules.noRules == true){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTiendaRule();
                        horario = horarioSeleccionado.horario;
                    }

                    if(rules.fastShipping == false && rules.inStorePickup == true && rules.noRules == true){
                        var horarioSeleccionado = shippingPayment.fechaRetiroTiendaRule();
                        horario = horarioSeleccionado.horario;
                    }

                }else {
                    horario =  'horario de envío no disponible';
                }
            }
            return horario;
        },

        /**
         *
         * @returns {*|string}
         */
        getUrbanoHours: function () {
            var shippingMethod = shippingPayment.shippingMethod();
            var selectedDistrict = shippingPayment.distrito();
            var shippingSettings = window.checkoutConfig.shippingSettings;

            if (shippingMethod && shippingMethod === 'urban') {
                var sendingHoursConfig = this.getMatchingOpeningConfig(selectedDistrict, shippingSettings);
                return sendingHoursConfig.sendingHours;
            }

            return 'Horario de envío no disponible';
        },

        /**
         *
         * @returns {*|string}
         */
        getUrbanoTime: function () {
            var shippingMethod = shippingPayment.shippingMethod();
            var selectedDistrict = shippingPayment.distrito();
            var shippingSettings = window.checkoutConfig.shippingSettings;

            if (shippingMethod && shippingMethod === 'urban') {
                var deliveryTimeConfig = this.getMatchingOpeningConfig(selectedDistrict, shippingSettings);
                return deliveryTimeConfig.deliveryTimeMessage;
            }

            return 'Información de entrega no disponible';
        },

        /**
         *
         * @param selectedDistrict
         * @param shippingSettings
         * @returns {{deliveryTimeMessage: *, sendingHours: *}|*}
         */
        getMatchingOpeningConfig: function (selectedDistrict, shippingSettings) {
            var selectedDistrictUpper = selectedDistrict.toUpperCase();

            // Verifica si alguno de los grupos de openings tiene el distrito seleccionado
            if (shippingSettings.openings_1.configuredLocations.toUpperCase().split(',').includes(selectedDistrictUpper)) {
                return {
                    sendingHours: shippingSettings.openings_1.sendingHours,
                    deliveryTimeMessage: shippingSettings.openings_1.deliveryTimeMessage
                };
            } else if (shippingSettings.openings_2.configuredLocations.toUpperCase().split(',').includes(selectedDistrictUpper)) {
                return {
                    sendingHours: shippingSettings.openings_2.sendingHours,
                    deliveryTimeMessage: shippingSettings.openings_2.deliveryTimeMessage
                };
            }

            // Devuelve configuraciones de respaldo si no hay un match
            return {
                sendingHours: shippingSettings.openings_1.restSendingHours,
                deliveryTimeMessage: shippingSettings.openings_1.restDeliveryTimeMessage
            };
        },


        /**
         * validate if it's ready to show summary
         * @returns {false|*}
         */
        isReadyToShowSummary: function () {
            return (!_.isUndefined(shippingPayment.isShippingStepFinished()) &&
                shippingPayment.isShippingStepFinished().includes("_complete"));

        }
    });
});
