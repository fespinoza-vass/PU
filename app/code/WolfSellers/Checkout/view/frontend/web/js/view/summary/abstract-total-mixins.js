define([], function () {
    'use strict';
    var mixin = {

        /**
         * total, subtotal
         * @return {boolean}
         */
        isFullMode: function () {
            if (!this.getTotals()) {
                return false;
            }
            return true;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
