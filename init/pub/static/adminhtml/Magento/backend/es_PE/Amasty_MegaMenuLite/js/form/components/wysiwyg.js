define([
    'jquery',
    'Magento_PageBuilder/js/form/element/wysiwyg'
], function ($, Wysiwyg) {
    'use strict';

    /**
     * Extend the original PageBuilder functionality
     */
    return Wysiwyg.extend({
        /**
         * Extends method show. Hide content at root category.
         */
        show: function () {
            this._super();

            if (this.level === 1) {
                this.visible(false);

                return false;
            }
        },

        /**
         * Hide notice.
         *
         * @returns {Object} Chainable.
         */
        hideNotice: function () {
            this.notice('');

            return this;
        },

        /**
         * Show notice.
         *
         * @returns {Object} Chainable.
         */
        showNotice: function () {
            this.notice(this.defaultNotice);

            return this;
        }
    });
});
