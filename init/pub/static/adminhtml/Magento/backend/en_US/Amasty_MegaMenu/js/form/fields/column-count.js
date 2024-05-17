/**
 * Amasty MegaMenu Column Count Field
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            activeLevel: 2,
            listens: {
                '${ $.provider }:data.hide_content': 'updateVisibility',
                '${ $.provider }:data.submenu_type': 'updateType'
            }
        },

        /**
         * Column Count Field init method
         */
        initialize: function () {
            var self = this;

            self._super();

            if (!this.validateVisibility()) {
                return false;
            }

            if (!parseInt(self.source.data.hide_content)) {
                self.visible(!parseInt(self.source.data.submenu_type));
            }
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

            if (parseInt(this.source.data.submenu_type)) {
                return false;
            }

            this.visible(!parseInt(value));
        },

        /**
         * Update Type method
         *
         * @params {String} value
         */
        updateType: function (value) {
            var visibility = !parseInt(value);

            if (!this.validateVisibility()) {
                return false;
            }

            if (parseInt(this.source.data.hide_content)) {
                return false;
            }

            this.visible(visibility);
        },

        /**
         * Validate Visibility method
         */
        validateVisibility: function () {
            return !this.hidden;
        }
    });
});
