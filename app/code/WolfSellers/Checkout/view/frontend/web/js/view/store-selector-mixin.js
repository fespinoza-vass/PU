define([
    'ko',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer'
], function (
    ko,
    shippingPayment,
    customer
) {
    'use strict';

    var storeSeletorMixin = {
        defaults:{
            links: {
                "goToResume":'checkout:isVisibleShipping'
            }
        },
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),
        goToResume:ko.observable(),

        initialize: function () {
            this._super();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                this.setIsDisabledShippingStep();
            },this);
        },
        /**
         * Update progress bar to complete or incomplete state
         */
        setIsDisabledShippingStep: function () {
            if (customer.isCustomerStepFinished() === '_complete'){
                this.isDisabledShippingStep(true);
            }else{
                this.isDisabledShippingStep(false);
            }
        },
        /**
         * overwrite original function 'cuz there isn't a form to validate.
         * @returns {boolean}
         */
        validatePickupInformation: function () {
            return true;
        },
        /**
         * click event for siguiente button
         */
        setPickupInformation: function () {
            if (this.validatePickupInformation()) {
                this.isShippingStepFinished("_complete");
                this.goToResume(false);
            }else{
                this.isShippingStepFinished("_active");
                this.goToResume(true);
            }
            this._super();
        }
    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});
