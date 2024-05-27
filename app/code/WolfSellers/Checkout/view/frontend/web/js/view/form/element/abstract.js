define([
    'mage/utils/wrapper'
], function (
    wrapper
) {
    'use strict';

    var abstractMixin = {
        /**
         * This it's a usually not allowed mixin but in this case its
         * necessary due to magento 2 native doesn't validate all uiComponent Forms
         * to add that functionality its necessary the following things:
         * 1)Declare as uiComponent type Form
         * 2)Override initConfig method an add a namespace with this:
         *      this.selector = '[data-form-part=' + this.namespace + ']';
         * 3)Make a function to validate this.source.set('params.namespace', false);
         * 4)Trigger validation with this.source.trigger('namespace.input.data.validation');
         * 5)After trigger validaion !this.source.get('params.namespace');
         * 6)Finally in the form template add the data-form-part="namespace" attribute
         * TODO make dataScope generally
         */
        validate: function () {
            var result,
                isValid = false;
            result = this._super();
            isValid = result.valid;
            if (this.source && this.dataScope.includes('customerData') && !isValid) {
                this.source.set('params.customerDataStepInvalid', true);
            }
            if (this.source && this.dataScope.includes('anotherPicker') && !isValid) {
                this.source.set('params.anotherPicker', true);
            }
            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }
        }
    };

    return function (abstractComponent) {
        return abstractComponent.extend(abstractMixin);
    }

})
