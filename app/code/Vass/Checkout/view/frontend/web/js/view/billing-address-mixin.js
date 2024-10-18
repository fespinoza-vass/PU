define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'uiRegistry'
], function ($, wrapper, quote, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({

            initialize: function () {
                let self = this;
                this._super();

                this.updateAddress = wrapper.wrap(this.updateAddress, function (originalMethod) {

                    let shippingAddress = quote.shippingAddress();

                    let formCustomer = $('#personal-information-form');
                    let formShipping = $('#co-shipping-form');

                    let inputFirstname = formCustomer.find('input[name="firstname"]');
                    if (!shippingAddress.firstname && !inputFirstname.val()) {
                        inputFirstname.focus();
                        return;
                    }

                    let inputLastname = formCustomer.find('input[name="lastname"]');
                    if (!shippingAddress.lastname && !inputLastname.val()) {
                        inputLastname.focus();
                        return;
                    }

                    let inputStreet = formShipping.find('input[name="street[0]"]');
                    if (
                        !shippingAddress.street[0] && !inputStreet.val()
                        || (inputStreet.val().length < 10 && shippingAddress.street[0].length < 10)) {
                        inputStreet.focus();
                        return;
                    }

                    let inputTelephone = formShipping.find('input[name="telephone"]');
                    if (
                        !shippingAddress.telephone && !inputTelephone.val()
                        || inputTelephone.val().length < 7
                        || !/^[0-9]+$/.test(inputTelephone.val())
                    ) {
                        inputTelephone.focus();
                        return;
                    }

                    let name = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.firstname');
                    let lastname = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.lastname');
                    let telephone = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.telephone');
                    let street = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.street.0');

                    name.value(shippingAddress.firstname);
                    lastname.value(shippingAddress.lastname);
                    telephone.value(shippingAddress.telephone);
                    street.value(shippingAddress.street[0]);

                    originalMethod();
                });

                $(document).on('click', '.billing-address-same-as-shipping-block', function () {
                    let input = $("[name='billing-address-same-as-shipping']");
                    let infoBill = $('.billing-info');

                    if (input.is(':checked')) {
                        infoBill.hide();
                    } else {
                        infoBill.show();
                    }

                    let rucInput = $('[name="custom_attributes[ruc]"]');
                    let ruc = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.ruc');
                    let nameCompany = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.razon_social');
                    let taxDirectory = registry.get('checkout.steps.billing-step.payment.afterMethods.billing-address-form.form-fields.direccion_fiscal');

                    rucInput.attr('maxlength', 11)
                    ruc.value('');
                    nameCompany.value('');
                    taxDirectory.value('');

                });

                return this;
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe({
                        saveInAddressBook: 0
                    });

                quote.billingAddress.subscribe(function (newAddress) {
                    this.saveInAddressBook(0);
                }, this);

                return this;
            },
        });
    };
});
