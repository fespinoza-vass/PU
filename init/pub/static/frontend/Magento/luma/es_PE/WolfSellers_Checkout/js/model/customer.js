define([
    'ko'
], function (ko) {
    'use strict';

    var email = '',
        customerName = '',
        customerLastName = '',
        customerTypeIdentification = '',
        customerNumberIdentification = '',
        customerTelephone = '',
        telephone = "",
        id = "",
        numId ="",
        passwordRegister ="",
        passwordConfirm = "",
        isCustomerLoggedIn = false;

    if(window.isCustomerLoggedIn){
        isCustomerLoggedIn = true;
        id = window.checkoutConfig.customerData.custom_attributes.identificacion;
        telephone =window.checkoutConfig.customerData.custom_attributes.telefono;
        numId =window.checkoutConfig.customerData.custom_attributes.numero_de_identificacion;
        email = window.checkoutConfig.customerData.email;
        customerName = window.checkoutConfig.customerData.firstname;
        customerLastName = window.checkoutConfig.customerData.lastname;
        customerTypeIdentification = id.value;
        customerNumberIdentification = numId.value;
        customerTelephone = telephone.value;
    }

    return {
        isCustomerLoggedIn: ko.observable(isCustomerLoggedIn),
        email : ko.observable(email),
        customerName : ko.observable(customerName),
        customerLastName : ko.observable(customerLastName),
        customerTypeIdentification : ko.observable(customerTypeIdentification),
        customerTypeIdentificationLabel : ko.observable(''),
        customerNumberIdentification : ko.observable(customerNumberIdentification),
        customerTelephone : ko.observable(customerTelephone),
        passwordRegister :ko.observable(passwordRegister),
        passwordConfirm : ko.observable(passwordConfirm),
        isCustomerStepFinished: ko.observable("_active")
    }

});
