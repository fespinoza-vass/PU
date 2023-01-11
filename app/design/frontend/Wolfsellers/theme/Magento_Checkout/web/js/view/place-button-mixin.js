define([
    'jquery',
    'ko',
    'uiRegistry',
    'Amasty_CheckoutStyleSwitcher/js/model/amalert',
    'Magento_Checkout/js/checkout-data',
    'mage/translate'
], function (
        $,
        ko,
        registry,
        alert,
        checkoutData
) {

    'use strict';

    var shippingAddressData = checkoutData.getShippingAddressFromData();

    var placeButtonMixin = {

        /**
         * Add shipping Address Validation to place order button
         */
        placeOrder: function () {
            var addressValidatorPath = "checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.shippingAdditional.address-validation-message.validator",
                errorMessage = '',
                visible = false,
                addressValidator;
            addressValidator = registry.get(addressValidatorPath);
            visible = $(".form-shipping-address").is(':visible');
            if (visible){
                if (addressValidator.addressData.country_id == "" ||
                    addressValidator.addressData.firstname == "" ||
                    addressValidator.addressData.lastname == "" ||
                    addressValidator.addressData.postcode == "" ||
                    addressValidator.addressData.region_id == "" ||
                    addressValidator.addressData.telephone == "" ||
                    addressValidator.addressData.street[0] == ""
                ) {
                    errorMessage = $.mage.__('No payment shipping address selected');
                    alert({ content: errorMessage });
                    return;
                }
            }
            
            this._super();
        }
    };

    return function (placeButton) {
        return placeButton.extend(placeButtonMixin);
    };
});
