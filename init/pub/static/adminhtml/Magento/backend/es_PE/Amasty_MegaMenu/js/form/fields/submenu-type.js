/**
 * Amasty MegaMenu Submenu Type Field
 */

define([
    'Magento_Ui/js/form/element/select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            activeLevel: 2,
            listens: {
                '${ $.provider }:data.hide_content': 'updateVisibility'
            }
        },

        /**
         * Submenu Type Field init method
         */
        initialize: function () {
            var self = this;

            self._super();

            if (!this.validateVisibility()) {
                return false;
            }

            self.visible(!parseInt(self.source.data.hide_content));
        },

        /**
         * Update Visibility method
         *
         * @params {String} value
         */
        updateVisibility: function (value) {
            if (!this.validateVisibility()) {
                return false;
            }

            this.visible(!parseInt(value));
        },

        /**
         * Validate Visibility method
         */
        validateVisibility: function () {
            return !this.hidden;
        }
    });
});
