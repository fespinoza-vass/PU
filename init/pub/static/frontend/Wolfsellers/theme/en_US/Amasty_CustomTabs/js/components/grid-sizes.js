/**
 * Table Sizes Component (For demo)
 *
 * @return widget with methods which help's in the implementation user interface
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('am.gridSizes', {
        options: {
            cellSelect: '.amtab-cell',
            lineSelect: '.amtab-table-line',
            hoverlineClass: '-amtab-hoverline',
            activeClass: '-active'
        },

        _create: function () {
            var self = this,
                lines = self.element.find(self.options.lineSelect);

            lines.find(self.options.cellSelect).click(function (e) {
                self._gridHighlight(e)
            });
        },

        /**
         * Method's for Highlighting cells in table like a coordinate grid
         */
        _gridHighlight: function (e) {
            var self = this;

            self._clearTable();
            self.targetElem = {
                    node: $(e.target).addClass(self.options.activeClass),
                    position: null
                };

            self._highlightRow();
            self._highlightColumn();
        },

        _highlightRow: function () {
            var self = this,
                targetLine = self.targetElem.node.closest(self.options.lineSelect);

            targetLine.addClass(self.options.activeClass);
            targetLine.children().each(function (i) {
                var elem = $(this);

                if (elem.hasClass(self.options.activeClass)) {
                    self.targetElem.position = i;

                    return false;
                }

                elem.addClass(self.options.hoverlineClass);
            });
        },

        _highlightColumn: function () {
            var self = this;

            self.element.children().each(function () {
                var elem = $(this);

                if (elem.hasClass(self.options.activeClass)) {
                    return false;
                }



                elem.children().eq(self.targetElem.position).addClass(self.options.hoverlineClass);
            });
        },

        _clearTable: function () {
            var self = this;

            self.element.find('.' + self.options.hoverlineClass).removeClass(self.options.hoverlineClass);
            self.element.find('.' + self.options.activeClass).removeClass(self.options.activeClass);
        }
    });

    return $.am.gridSizes;
});
