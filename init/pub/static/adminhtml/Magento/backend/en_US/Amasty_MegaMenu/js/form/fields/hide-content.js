/**
 * Amasty MegaMenu Hide Content Field
 */

define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            activeLevel: 1,
        },

        /**
         * Subcategories Position Field init method
         */
        initialize: function () {
            var self = this;

            self._super();

            self.visible(self.validateVisibility());
        },


        /**
         * Validate Visibility method
         */
        validateVisibility: function () {
            return !this.hidden;
        }
    });
});
