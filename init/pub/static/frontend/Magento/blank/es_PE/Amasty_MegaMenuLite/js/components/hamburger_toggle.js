/**
 *  Amasty Hamburger toggle UI Component
 */

define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            links: {
                color_settings: 'index = ammenu_wrapper:color_settings'
            }
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isOpen: false
                });

            return this;
        },

        /**
         *  Toggling open state method
         *  @return {void}
         */
        toggling: function () {
            this.isOpen(!this.isOpen());
        }
    });
});
