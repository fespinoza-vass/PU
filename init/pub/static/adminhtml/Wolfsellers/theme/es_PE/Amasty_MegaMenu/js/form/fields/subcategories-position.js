/**
 * Amasty MegaMenu Subcategories Position Field
 */

define([
    'Magento_Ui/js/form/element/select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            activeLevel: 2,
            listens: {
                '${ $.provider }:data.hide_content': 'updateVisibility',
                '${ $.provider }:data.submenu_type': 'updateVisibility'
            }
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
         * Update Visibility method
         */
        updateVisibility: function () {
            this.visible(this.validateVisibility());
        },

        /**
         * Validate Visibility method
         */
        validateVisibility: function () {
            return !this.hidden && !parseInt(this.source.data.hide_content) && (parseInt(this.source.data.submenu_type) || parseInt(this.level) > this.activeLevel);
        }
    });
});
