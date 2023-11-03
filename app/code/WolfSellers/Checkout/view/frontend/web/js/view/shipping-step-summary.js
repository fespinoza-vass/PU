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
        isShipping: ko.observable(true),
        isStorePickUp: ko.observable(false),
        initialize:function () {
            this._super();
            shippingPayment.shippingMethod.subscribe(function (value) {
                if (value.includes('instore')){
                    this.isShipping(false);
                    this.isStorePickUp(true);
                }else{
                    this.isShipping(true);
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
                if (shippingPayment.shippingMethod().includes("flat")){
                    return "Envió regular a domicilio";
                }
                if (shippingPayment.shippingMethod().includes("rapido")){
                    return "Envió rápido a domicilio";
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
            if (this.isReadyToShowSummary()){
                return shippingPayment.referencia();
            }
            return "Calculando...";
        },
        /**
         * Add text value for store pickup selected input
         * @returns {string}
         */
        getStorePickUpSelected: function () {
            if (this.isReadyToShowSummary()){
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
            if (shippingPayment.shippingMethod().includes("flat")){
                if(distrito.length >= 3){
                    distrito = distrito.charAt(0).toUpperCase() + distrito.slice(1).toLowerCase();
                }
                if(departamento.length >= 3){
                    departamento = departamento.charAt(0).toUpperCase() + departamento.slice(1).toLowerCase();
                }
                if(provincia.length >= 3){
                    provincia = provincia.charAt(0).toUpperCase() + provincia.slice(1).toLowerCase();
                }
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                if(distrito.length >= 3){
                    distrito = distrito.charAt(0).toUpperCase() + distrito.slice(1).toLowerCase();
                }
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
            if (shippingPayment.shippingMethod().includes("flat")){
                date = "2 días naturales";
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                var horarioSeleccionado = shippingPayment.fechaEnvioRapido();
                date = horarioSeleccionado.fecha
            }
            if (shippingPayment.shippingMethod().includes("instore")){
                var ahora = new Date();
                var fechaEntrega = wolfUtils.formatDate(ahora);
                date = "Hoy " +  fechaEntrega;
            }
            return date;
        },
        /**
         * get shipping time for shipping summary
         * @returns {string}
         */
        getShippingTime:function () {
            var horario = "";
            if (shippingPayment.shippingMethod().includes("flat")){
                horario = "en un rango de 8 am a 7 pm";
            }
            if (shippingPayment.shippingMethod().includes("rapido")){
                var horarioSeleccionado = shippingPayment.fechaEnvioRapido();
                horario = horarioSeleccionado.horario;
            }
            if (shippingPayment.shippingMethod().includes("instore")){
                horario = "8 am a 9:30pm";
            }
            return horario;
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
