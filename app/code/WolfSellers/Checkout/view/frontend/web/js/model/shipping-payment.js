define([
    'ko',
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
],function (
    ko,
    _,
    registry,
    quote
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
        fechaEnvioRapido: ko.observable(""),
        horarioSeleccionado: ko.observable(""),


        shippingMethod: ko.observable(""),

        tiendaSeleccionada: ko.observable(""),
        direccionTienda: ko.observable(""),
        picker: ko.observable(""),
        identificacionPicker: ko.observable(""),
        numero_identificacion: ko.observable(""),
        nombreApellido: ko.observable(""),
        correoOpcional: ko.observable(""),
        distrito_comprobante: ko.observable(""),
        direccion_comprobante: ko.observable(""),
        horarioTienda: ko.observable(""),
        fechaRetiroTienda: ko.observable(""),
        fechaRetiroTiendaRule: ko.observable(""),
        /**
         * Set shipping method to model with text values
         * @param quote
         */
        setShippingMethodModelData: function () {
            if(_.isNull(quote.shippingMethod()) || _.isUndefined(quote.shippingMethod())){
                return false;
            }
            if (quote.shippingMethod().carrier_code.includes("urban")){
                this.shippingMethod("urban");
            }
            if (quote.shippingMethod().carrier_code.includes("free")){
                this.shippingMethod("free");
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
        setShippingModelData: function () {
            this.nombre(quote.shippingAddress().firstname);
            this.apellido(quote.shippingAddress().lastname);
            this.telefono(quote.shippingAddress().telephone);
            this.dni(this.getCustomAttributeByAttributeCode(quote,"vat_id"));

            this.factura(this.getCustomAttributeByAttributeCode(quote,"invoice_required"));
            this.empresa(this.getCustomAttributeByAttributeCode(quote,"company"));
            this.ruc(this.getCustomAttributeByAttributeCode(quote,"dni"));

            if(this.shippingMethod().includes("urban") || this.shippingMethod().includes("free")){
                this.referencia(this.getCustomAttributeByAttributeCode(quote,"referencia_envio"));
                this.direccion(quote.shippingAddress().street[0]);
                this.departamento(quote.shippingAddress().region);
                this.provincia(quote.shippingAddress().city);
                this.distrito(this.getCustomAttributeByAttributeCode(quote,"colony"));
            }
            if(this.shippingMethod().includes("rapido")){
                this.referencia(this.getCustomAttributeByAttributeCode(quote,"referencia_envio"));
                this.direccion(quote.shippingAddress().street[0]);
                this.distritoEnvioRapido(this.getCustomAttributeByAttributeCode(quote,"distrito_envio_rapido"));
                var horarios_disponibles = registry.get("checkout.steps.shipping-step.shippingAddress.schedule.schedule");
                this.horarioSeleccionado(horarios_disponibles.optionSelected());
            }
        },
        /**
         * Set data when its pick up
         * @param selectedLocation
         */
        setPickupModelData: function (selectedLocation) {
            this.tiendaSeleccionada(selectedLocation.name);
            this.direccionTienda(selectedLocation.street[0]);
            var picker = registry.get("checkout.steps.store-pickup.store-selector.picker.pickerOption");
            this.picker(picker.value());
            var identificacion = registry.get("checkout.steps.store-pickup.store-selector.another-picker.identificacion_picker");
            this.identificacionPicker(identificacion.value());
            if (!identificacion.value()){
                this.identificacionPicker('');
            }
            var num_identificacion = registry.get("checkout.steps.store-pickup.store-selector.another-picker.numero_identificacion_picker");
            this.numero_identificacion(num_identificacion.value());
            var nombre_completo_picker = registry.get("checkout.steps.store-pickup.store-selector.another-picker.nombre_completo_picker");
            this.nombreApellido(nombre_completo_picker.value());
            var emailPicker = registry.get("checkout.steps.store-pickup.store-selector.another-picker.email_picker");
            this.correoOpcional(emailPicker.value());
            var voucher = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.voucher");
            this.distrito_comprobante(voucher.value());
            var direccion_comprobante = registry.get("checkout.steps.store-pickup.store-selector.picker-voucher.direccion_comprobante_picker");
            this.direccion_comprobante(direccion_comprobante.value());
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
