define([
    'mage/utils/wrapper'
], function (
    wrapper
) {
    'use strict';

    var abstractMixin = {
        validate: function () {
            var result,
                isValid = false;
            result = this._super();
            isValid = result.valid;
            if (this.source && this.dataScope.includes('customerData') && !isValid) {
                this.source.set('params.customerDataStepInvalid', true);
            }

        }
    };

    return function (abstractComponent) {
        return abstractComponent.extend(abstractMixin);
    }

})
