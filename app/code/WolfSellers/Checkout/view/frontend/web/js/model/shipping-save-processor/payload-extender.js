define([
    'jquery',
    'underscore',
    'WolfSellers_Checkout/js/model/customer',
    'WolfSellers_Checkout/js/model/shipping-payment'
], function (
    $,
    _,
    customer,
    shippingPayment
) {
    'use strict';

    return function (payload) {
        var payloadEnvioRegular = {
            metodo_envio:"",
            departamento:"",
            provincia:"",
            distrito:"",
            direccion:"",
            referencia:""
        };
        var payloadEnvioRapido = {
            distrito:"",
            direccion:"",
            referencia:"",
            horarioSeleccionado:""
        };
        var payloadRetiroEnTienda = {
            picker:"",
            identificacion:"",
            numero_identificacion:"",
            nombreApellido:"",
            correoOpcional:"",
            distrito_comprobante:"",
            direccion_comprobante:""
        };
        if(!_.isUndefined(shippingPayment.shippingMethod())){
            if (shippingPayment.shippingMethod() === 'urban' || shippingPayment.shippingMethod() === 'free'){
                payloadEnvioRegular.metodo_envio = shippingPayment.shippingMethod();
                payloadEnvioRegular.departamento = shippingPayment.departamento();
                payloadEnvioRegular.provincia = shippingPayment.provincia();
                payloadEnvioRegular.distrito = shippingPayment.distrito();
                payloadEnvioRegular.direccion = shippingPayment.direccion();
                payloadEnvioRegular.referencia = shippingPayment.referencia();
            }
            if (shippingPayment.shippingMethod() === "rapido"){
                payloadEnvioRapido.distrito = shippingPayment.distritoEnvioRapido();
                payloadEnvioRapido.direccion = shippingPayment.direccion();
                payloadEnvioRapido.referencia = shippingPayment.referencia();
                payloadEnvioRapido.horarioSeleccionado = shippingPayment.horarioSeleccionado();
                payload.addressInformation.shipping_address.region = "Lima";
                payload.addressInformation.shipping_address.regionId = '2935';
                payload.addressInformation.shipping_address.regionCode = 'PE-LIM';
                payload.addressInformation.shipping_address.city = 'LIMA';
                payload.addressInformation.billing_address.region = "Lima";
                payload.addressInformation.billing_address.regionId = '2935';
                payload.addressInformation.billing_address.regionCode = 'PE-LIM';
                payload.addressInformation.billing_address.city = 'LIMA';
            }
            if (shippingPayment.shippingMethod() === "instore"){
                payloadRetiroEnTienda.picker = shippingPayment.picker()
                payloadRetiroEnTienda.identificacion = shippingPayment.identificacionPicker();
                payloadRetiroEnTienda.numero_identificacion = shippingPayment.numero_identificacion();
                payloadRetiroEnTienda.nombreApellido = shippingPayment.nombreApellido();
                payloadRetiroEnTienda.correoOpcional = shippingPayment.correoOpcional();
                payloadRetiroEnTienda.distrito_comprobante = shippingPayment.distrito_comprobante();
                payloadRetiroEnTienda.direccion_comprobante = shippingPayment.direccion_comprobante();
                payload.addressInformation.billing_address = payload.addressInformation.shipping_address;
            }
        }

        payload.addressInformation['extension_attributes'] = {
            customer_name : customer.customerName(),
            customer_apellido :customer.customerLastName(),
            customer_telefono :customer.customerTelephone(),
            customer_identificacion :customer.customerTypeIdentification(),
            customer_numero_de_identificacion: customer.customerNumberIdentification(),
            customer_password : customer.passwordRegister(),
            envio_regular: payloadEnvioRegular,
            envio_rapido: payloadEnvioRapido,
            retiro_tienda: payloadRetiroEnTienda
        }
        return payload;
    };
});
