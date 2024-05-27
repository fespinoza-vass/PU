/**
 *  Amasty Category Columns UI Component
 */

define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_MegaMenuLite/components/tree/columns'
        },

        /**
         * Init Target elements method
         *
         * @public
         * @params {Object} elem - current data
         * @return {void}
         */
        init: function (elem) {
            this._initColumnCount(elem);
        },

        /**
         * Init Columns Count for current Elem
         *
         * @public
         * @params {Object} elem - current root element
         * @return {void}
         */
        _initColumnCount: function (elem) {
            if (elem.column_count() === 0) {
                elem.column_count(elem.elems.length);
            }

            if (elem.column_count() > elem.elems.length) {
                elem.column_count(elem.elems.length);
            }
        }
    });
});
