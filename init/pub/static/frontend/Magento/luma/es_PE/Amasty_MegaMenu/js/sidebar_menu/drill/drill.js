/**
 *  Amasty Drill Menu elements UI Component
 */

define([
    'uiComponent',
    'ammenu_helpers'
], function (Component, helpers) {
    'use strict';

    return Component.extend({
        defaults: {
            templates: {
                navigation: 'Amasty_MegaMenu/sidebar_menu/drill/navigation',
                current_title: 'Amasty_MegaMenu/sidebar_menu/drill/current_title',
                activeLevel: 'Amasty_MegaMenu/sidebar_menu/drill/active_level'
            },
            imports: {
                mobile_class: 'index = ammenu_wrapper:mobile_class',
                color_settings: 'index = ammenu_wrapper:color_settings',
                icons: 'index = ammenu_wrapper:icons',
                is_icons_available: 'index = ammenu_wrapper:is_icons_available',
                root_templates: 'index = ammenu_wrapper:templates',
                activeTab: 'index = ammenu_tabs_wrapper:activeTab',
                hide_view_all_link: 'index = ammenu_wrapper:hide_view_all_link'
            }
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            var self = this;

            self._super()
                .observe({
                    elems: [],
                    actionAnimation: '',
                    activeTab: 0,
                    activeElem: false
                });

            self.activeTab.subscribe(function () {
                self.activeElem(false);
            });

            self.activeElem.subscribe(function (elem) {
                helpers.setItemFocus(elem);
            });

            return self;
        },

        /**
         * Toggling button method
         *
         * @params {Object} elem
         * @return {Boolean} for propagation
         */
        toggleItem: function (elem) {
            this.actionAnimation(false);
            this.activeElem(elem);
            this.actionAnimation('-slide-left');
        },

        /**
         * Set root category method
         * @return {void}
         */
        setRootLevel: function () {
            this.actionAnimation(false);
            this.activeElem(false);
            this.actionAnimation('-slide-right');
        },

        /**
         * Set previous category method
         * @return {void}
         */
        setPrevLevel: function () {
            this.actionAnimation(false);
            if (!this.activeElem().parent.isRoot) {
                this.activeElem(this.activeElem().parent);
            } else {
                this.activeElem(false);
            }

            this.actionAnimation('-slide-right');
        }
    });
});
