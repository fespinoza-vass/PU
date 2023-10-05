define([
    'jquery',
    'WolfSellers_Checkout/js/model/customer'
], function (
    $,
    customer
) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {
            customer_name : customer.customerName(),
            customer_apellido :customer.customerLastName(),
            customer_telefono :customer.customerTelephone(),
            customer_identificacion :customer.customerTypeIdentification(),
            customer_numero_de_identificacion: customer.customerNumberIdentification(),
            customer_password : customer.passwordRegister()
        }
        return payload;
    };
});
