define([

], function (

) {
    'use strict';

    var storeSeletorMixin = {
        validatePickupInformation: function () {
            return true;
        }
    };

    return function (storeSelectorTarget) {
        return storeSelectorTarget.extend(storeSeletorMixin);
    }

});
