define([
    'ko'
], function (ko) {
    'use strict';

    var email = '',
        customerName = '',
        customerLastName = '',
        customerTypeIdentification = '',
        customerNumberIdentification = '',
        customerTelephone = ''

    if(window.isCustomerLoggedIn){
        email = window.checkoutConfig.customerData.email;
        customerName = window.checkoutConfig.customerData.firstname;
        customerLastName = window.checkoutConfig.customerData.lastname;

    }

    return {
        email : ko.observable(email),
        customerName : ko.observable(customerName),
        customerLastName : ko.observable(customerLastName),
        customerTypeIdentification : ko.observable(customerTypeIdentification),
        customerNumberIdentification : ko.observable(customerNumberIdentification),
        customerTelephone : ko.observable(customerTelephone)
    }

});
