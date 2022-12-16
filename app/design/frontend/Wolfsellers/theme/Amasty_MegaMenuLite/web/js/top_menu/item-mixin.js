/**
 *  Amasty Sidebar Menu UI Component
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * @inheritDoc
             */
            initialize: function () {
                var self = this;
                self._super();

                $("li.ammenu-item").mouseover(function() {
                    var elem = $(this);
                    $("li.ammenu-item").removeClass('ammmenu-current-active');
                    elem.addClass('ammmenu-current-active');
                });

                $("li.ammenu-item").mouseleave(function() {
                    $( this ).removeClass('ammmenu-current-active');
                });
            }
        });
    }
});
