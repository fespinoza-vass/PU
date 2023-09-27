define([
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'WolfSellers_Checkout/js/model/customer',
    'WolfSellers_Checkout/js/model/shipping-payment'
], function (
    ko,
    stepNavigator,
    Customer,
    StepTwo
) {
    'use strict';
    var steps = stepNavigator.steps;

    var progressBarMixin = {
        defaults: {
            template: 'WolfSellers_Checkout/progress-bar'
        },
        stepOne: ko.observable(Customer.isCustomerStepFinished()),
        stepTwo: ko.observable(StepTwo.isStepTwoFinished()),//depends on model shipping/payment
        stepTree: ko.observable("_active"),//at placeORderaction changes to _complete
        stepIndexes: ko.observableArray([]) ,

        initialize:function () {
            this._super();
            this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);
            Customer.isCustomerStepFinished.subscribe(function (value) {
                this.stepOne(value);
                this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);
            },this);
        },

        getTitle:function (step) {
            return step.title;
        },
        getStepObservable: function (stepIndex) {
            return this.stepIndexes()[stepIndex];
        }
    };

    return function (progressBarTarget) {
        return progressBarTarget.extend(progressBarMixin);
    }
})
