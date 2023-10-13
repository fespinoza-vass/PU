define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/customer'
],function (
    $,
    ko,
    stepNavigator,
    $t,
    quote,
    shippingPayment,
    customer
) {
    'use strict';
    var shippingMixin = {
        defaults:{
            template: 'WolfSellers_Checkout/shipping',
        },
        isActive: ko.observable(false),
        isShippingStepFinished: ko.observable(false),
        isDisabledShippingStep: ko.observable(true),

        initialize: function () {
            this._super();
            var modifyData= {
                title : $t('Entrega y Pago')
            }
            stepNavigator.modifyStep("shipping", modifyData);
            this.setIsDisabledShippingStep();
            this.isShippingStepFinished.subscribe(function (value) {
                shippingPayment.isShippingStepFinished(value);
                this.setIsDisabledShippingStep();
            },this);

            return this;
        },
        /**
         * Overwrite set shipping information action
         * @returns {*}
         */
        setShippingInformation:function () {
            if (this.validateShippingInformation()) {
                this.isShippingStepFinished("_complete");
            }else{
                this.isShippingStepFinished("_active");
            }
            return this._super();
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
        }
    }


    return function(shippingTarget){
        return shippingTarget.extend(shippingMixin);
    }



});
