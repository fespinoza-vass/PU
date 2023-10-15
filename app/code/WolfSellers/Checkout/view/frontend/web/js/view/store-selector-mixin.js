define([
    'ko',
    'WolfSellers_Checkout/js/utils-wolf-uicomponents',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer'
], function (
    ko,
    wolfUtils,
    quote,
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
                console.log("isShippingStepFinishedFromPickUp:" + value);
                shippingPayment.isShippingStepFinished(value);
                shippingPayment.setShippingMethodModelData(quote);
                shippingPayment.setPickupModelData(this.selectedLocation());
                this.setIsDisabledShippingStep();
            },this);
            this.goToResume.subscribe(function (value) {
                //TODO Call here setIsDisabledShippingStep to update isShippingStepFinished
                console.log("hola");
            },this);
        },
        /**
         * Overwrite set pickup information action
         */
        setPickupInformation: function () {
            if (this.validatePickupInformation()) {
                this.isShippingStepFinished.notifySubscribers("_complete");
                this.goToResume(false);
            }else{
                this.isShippingStepFinished("_active");
                this.goToResume(true);
            }
            this._super();
        },
        /**
         * Update progress bar to complete or incomplete state
         * TODO validate isDisabledShippingStep because could be _complete, _active and empty
         * but empty its not implemented yet
         */
        setIsDisabledShippingStep: function () {
            if (customer.isCustomerStepFinished() === '_complete'){
                this.isDisabledShippingStep(true);
            }else{
                this.isDisabledShippingStep(false);
            }
        },
        /**
         * overwrite original function because there isn't a form to validate.
         * @returns {boolean}
         */
        validatePickupInformation: function () {
            return true;
        },

    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});
