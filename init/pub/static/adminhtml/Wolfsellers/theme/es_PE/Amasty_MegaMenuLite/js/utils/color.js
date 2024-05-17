/**
 * Amasty MegaMenu Colors Helper Component
 */

define([
    'jquery',
    'jquery-ui-modules/effect'
], function ($) {
    'use strict';

    return {

        /**
         * Generate darken color depending on the specified percentage
         *
         * @params {String} color - any color format
         * @params {Number} percent - relative values from 0.1 to 0.9
         *
         * @return {String} new color in rgba
         */
        getDarken: function (color, percent) {
            var currentHsla = $.Color(color).hsla();

            currentHsla[2] = currentHsla[2] - percent;

            return $.Color(color).hsla(currentHsla).toRgbaString();
        },

        /**
         * Generate lighten color depending on the specified percentage
         *
         * @params {String} color - any color format
         * @params {Number} percent - relative values from 0.1 to 0.9
         *
         * @return {String} new color in rgba
         */
        getLighten: function (color, percent) {
            var currentHsla = $.Color(color).hsla();

            currentHsla[2] = currentHsla[2] + percent;

            return $.Color(color).hsla(currentHsla).toRgbaString();
        },

        /**
         * Generate altered color depending on the specified percentage
         * and side of changes depends on brightness
         *
         * @params {String} color - any color format
         * @params {Number} percent - relative values from 0.1 to 0.9
         *
         * @return {String} new color in rgba
         */
        getAltered: function (color, percent) {
            var currentHsla = $.Color(color).hsla(),
                currentBrightness = currentHsla[2];

            if (currentBrightness > 0.7) {
                return this.getDarken(color, percent);
            }

            return this.getLighten(color, percent);
        }
    };
});

