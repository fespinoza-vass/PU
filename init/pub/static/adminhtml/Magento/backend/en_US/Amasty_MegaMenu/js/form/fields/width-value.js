/**
 * Amasty MegaMenu Width Value Field
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                '${ $.provider }:data.hide_content': 'updateVisibility',
                '${ $.provider }:data.width': 'updateType'
            }
        },

        /**
         * Width Value field init method
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
         * Update Type method
         */
        updateType: function () {
            this.visible(this.validateVisibility());
        },

        /**
         * Validate Visibility method
         */
        validateVisibility: function () {
            return !this.hidden && this.source.data.width === '2';
        }
    });
});
