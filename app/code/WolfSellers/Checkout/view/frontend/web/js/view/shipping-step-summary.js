define([
    'ko',
    'underscore',
    'uiComponent',
    'WolfSellers_Checkout/js/model/shipping-payment',
], function (
    ko,
    _,
    Component,
    shippingPayment
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
         * validate if it's ready to show summary
         * @returns {false|*}
         */
        isReadyToShowSummary: function () {
            return (!_.isUndefined(shippingPayment.isShippingStepFinished()) &&
                shippingPayment.isShippingStepFinished().includes("_complete"));

        }
    });
});
