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
                this._super();

                this.updateAddress = wrapper.wrap(this.updateAddress, function (originalMethod) {

                    var shippingAddress = quote.shippingAddress();

                    var name = registry.get('checkout.steps.shipping-step.shippingAddress.billing-address-form.form-fields.firstname');
                    var lastname = registry.get('checkout.steps.shipping-step.shippingAddress.billing-address-form.form-fields.lastname');
                    var street = registry.get('checkout.steps.shipping-step.shippingAddress.billing-address-form.form-fields.street[0]');
                    var telephone = registry.get('checkout.steps.shipping-step.shippingAddress.billing-address-form.form-fields.telephone');
                    name.value(shippingAddress.firstname);
                    lastname.value(shippingAddress.lastname);
                    // street.value(shippingAddress.street);
                    telephone.value(shippingAddress.telephone);

                    originalMethod();   
                });

                return this;
            }
        });
    };
});
