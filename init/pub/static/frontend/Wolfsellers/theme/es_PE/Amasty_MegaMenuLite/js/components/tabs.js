/**
 *  Amasty Account UI Component
 */

define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_MegaMenuLite/sidebar_menu/tabs_switcher',
            imports: {
                color_settings: 'index = ammenu_wrapper:color_settings',
                isOpen: 'index = ammenu_hamburger_toggle:isOpen'
            }
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    activeTab: 0,
                    isOpen: false,
                    tabsList: [
                        { title: $.mage.__('Menu') },
                        { title: $.mage.__('Account') }
                    ]
                });

            return this;
        }
    });
});
