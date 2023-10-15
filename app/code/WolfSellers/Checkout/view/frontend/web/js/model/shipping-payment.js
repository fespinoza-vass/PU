define([
    'ko',
    'underscore'
],function (
    ko,
    _
) {
    'use strict';

    return {
        isShippingStepFinished: ko.observable(""),
        isPaymentStepFinished: ko.observable(""),
        isStepTwoFinished: ko.observable(""),
        isPaymentFinished: ko.observable(""),
        nombre: ko.observable(""),
        apellido: ko.observable(""),
        telefono: ko.observable(""),
        referencia: ko.observable(""),
        dni: ko.observable(""),
        distritoEnvioRapido: ko.observable(""),
        distrito: ko.observable(""),
        provincia: ko.observable(""),
        departamento: ko.observable(""),
        factura: ko.observable(""),
        empresa: ko.observable(""),
        ruc: ko.observable(""),
        direccion: ko.observable(""),

        shippingMethod: ko.observable(""),

        tiendaSeleccionada: ko.observable(""),
        direccionTienda: ko.observable(""),
        /**
         * Set shipping method to model with text values
         * @param quote
         */
        setShippingMethodModelData: function (quote) {
            if (quote.shippingMethod().carrier_code.includes("flat")){
                this.shippingMethod("flat");
            }
            if (quote.shippingMethod().carrier_code.includes("rapido")){
                this.shippingMethod("rapido");
            }
            if (quote.shippingMethod().carrier_code.includes("instore")){
                this.shippingMethod("instore");
            }
        },
        /**
         * Set data when is shipping
         * @param quote
         */
        setShippingModelData: function (quote) {
            this.nombre(quote.shippingAddress().firstname);
            this.apellido(quote.shippingAddress().lastname);
            this.telefono(quote.shippingAddress().telephone);
            this.referencia(this.getCustomAttributeByAttributeCode(quote,"referencia_envio"));
            this.dni(this.getCustomAttributeByAttributeCode(quote,"vat_id"));
            this.distritoEnvioRapido(this.getCustomAttributeByAttributeCode(quote,"distrito_envio_rapido"));
            this.distrito(this.getCustomAttributeByAttributeCode(quote,"colony"));
            this.provincia(quote.shippingAddress().city);
            this.departamento(quote.shippingAddress().regionId);
            this.factura(this.getCustomAttributeByAttributeCode(quote,"invoice_required"));
            this.empresa(this.getCustomAttributeByAttributeCode(quote,"company"));
            this.ruc(this.getCustomAttributeByAttributeCode(quote,"dni"));
            this.direccion(quote.shippingAddress().street[0]);
        },
        /**
         * Set data when its pick up
         * @param selectedLocation
         */
        setPickupModelData: function (selectedLocation) {
            this.tiendaSeleccionada(selectedLocation.name);
            this.direccionTienda(selectedLocation.street[0]);
        },
        /**
         * Get custom Attributes from quote by attribute code
         * @param quote
         * @param attributeCode
         * @returns {*}
         */
        getCustomAttributeByAttributeCode: function (quote, attributeCode) {
            var result = _.find(quote.shippingAddress().customAttributes,
                {'attribute_code':attributeCode});
            if (result){
                return result.value;
            }
        }
    }
})
