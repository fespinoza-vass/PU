define([
    'ko'
], function (
    ko
) {
    'use strict';

    var storeSeletorMixin = {
        /**
         * overwrite original function 'cuz there isn't a form to validate.
         * @returns {boolean}
         */
        validatePickupInformation: function () {
            return true;
        }
    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});
