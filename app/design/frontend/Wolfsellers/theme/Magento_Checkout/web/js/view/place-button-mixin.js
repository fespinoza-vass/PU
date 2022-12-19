define([
    'jquery',
    'ko',
    'uiRegistry',
    'Amasty_CheckoutStyleSwitcher/js/model/amalert',
    'mage/translate'
], function (
        $,
        ko,
        registry,
        alert
) {

    'use strict';

    var placeButtonMixin = {
        /**
         * Add shipping Address Validation to place order button
         */
        placeOrder: function () {
            var addressValidatorPath = "checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.shippingAdditional.address-validation-message.validator",
                errorMessage = '',
                addressValidator;
            addressValidator = registry.get(addressValidatorPath);

            if (!addressValidator.isAddressValid) {
                errorMessage = $.mage.__('No payment shipping address selected');
                alert({ content: errorMessage });
                return;
            }
            this._super();
        }
    };

    return function (placeButton) {
        return placeButton.extend(placeButtonMixin);
    };
});
