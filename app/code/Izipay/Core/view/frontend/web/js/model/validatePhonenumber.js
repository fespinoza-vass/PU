define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function(value) {
                //var billingData = quote.billingAddress._latestValue;
                //return utils.isEmptyNoTrim(value) || !/^\D*(?:\d\D*){7,15}$/.test(value);
                //return /^\D*(?:\d\D*){7,15}$/.test(value);
                return true;
            }
        }
    }
);