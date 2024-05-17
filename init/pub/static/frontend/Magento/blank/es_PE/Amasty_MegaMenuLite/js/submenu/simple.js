/**
 *  Amasty simple submenu UI Component
 */

define([
    'uiComponent',
    'ammenu_helpers'
], function (Component, helpers) {
    'use strict';

    return Component.extend({
        defaults: {
            activeElem: false,
            template: 'Amasty_MegaMenuLite/submenu/simple/wrapper',
            imports: {
                color_settings: 'index = ammenu_wrapper:color_settings',
                is_icons_available: 'index = ammenu_wrapper:is_icons_available',
                root_templates: 'index = ammenu_wrapper:templates',
                animation_time: 'index = ammenu_wrapper:animation_time'
            }
        },

        /**
         * Simple menu init method
         *
         * @params {Object} element - node element
         * @params {Object} context - view model
         * @return {void}
         */
        init: function (element, context) {
            helpers.applyBindings(element, context);
            helpers.sliderResizeSubscribe(element, context.elem.isActive);
            helpers.updateFormKey(element);
        }
    });
});
