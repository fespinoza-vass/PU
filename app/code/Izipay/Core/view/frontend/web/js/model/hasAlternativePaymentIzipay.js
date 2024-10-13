define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var AlternativeIzipayPaymentValidationResult = true;
                var has_izipay_alternative_payments = $('[name="has-izipay-alternative-methods"]').val();

                if (has_izipay_alternative_payments == "true") {
                    var izipay_alternative_payment_method_selected = $('[name="izipay-alternative-payment"]:checked').val();
                    if (!izipay_alternative_payment_method_selected) {
                        AlternativeIzipayPaymentValidationResult = false;
                        $(".error-validation-izipay-payment-method").css("display", "block");
                    } else {
                        $(".error-validation-izipay-payment-method").css("display", "none");
                    }
                }

                return AlternativeIzipayPaymentValidationResult;
            }
        };
    }
);
