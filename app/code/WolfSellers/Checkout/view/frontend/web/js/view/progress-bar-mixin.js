define([
    'ko',
    'Magento_Checkout/js/model/step-navigator',
    'WolfSellers_Checkout/js/model/customer',
    'WolfSellers_Checkout/js/model/shipping-payment',
    'WolfSellers_Checkout/js/model/step-summary'
], function (
    ko,
    stepNavigator,
    Customer,
    StepTwo,
    StepTree
) {
    'use strict';
    var steps = stepNavigator.steps;

    var progressBarMixin = {
        defaults: {
            template: 'WolfSellers_Checkout/progress-bar'
        },
        stepOne: ko.observable(Customer.isCustomerStepFinished()),
        stepTwo: ko.observable(StepTwo.isStepTwoFinished()),//depends on model shipping/payment
        stepTree: ko.observable(StepTree.isStepTreeFinished()),//at placeOrderAction changes to _complete
        stepIndexes: ko.observableArray([]) ,


        initialize:function () {
            this._super();
            this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);

            Customer.isCustomerStepFinished.subscribe(function (value) {
                this.stepOne(value);
                this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);
            },this);
            StepTwo.isStepTwoFinished.subscribe(function (value){
                this.stepTwo(value);
                this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);
            },this);
            StepTree.isStepTreeFinished.subscribe(function (value){
                this.stepTree(value);
                this.stepIndexes([this.stepOne(),this.stepTwo(),this.stepTree()]);
            },this);
        },
        /**
         * Get Progress bar tittles
         * @param step
         * @returns {*}
         */
        getTitle:function (step) {
            return step.title;
        },
        /**
         * Get Step Observable by index
         * @param stepIndex
         * @returns {*}
         */
        getStepObservable: function (stepIndex) {
            return this.stepIndexes()[stepIndex];
        }
    };

    return function (progressBarTarget) {
        return progressBarTarget.extend(progressBarMixin);
    }
})
