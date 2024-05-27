/**
 *  Amasty Sidebar Menu UI Component
 */

define([
    'uiComponent',
    'ammenu_helpers',
    'underscore',
    'mage/translate'
], function (Component, helpers, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_MegaMenuLite/sidebar_menu/sidebar_menu',
            templates: {
                itemsAccordion: 'Amasty_MegaMenuLite/sidebar_menu/accordion/items/wrapper'
            },
            components: [
                'index = ammenu_wrapper'
            ],
            imports: {
                hamburger_animation: 'index = ammenu_wrapper:hamburger_animation',
                animation_time: 'index = ammenu_wrapper:animation_time',
                root_templates: 'index = ammenu_wrapper:templates',
                color_settings: 'index = ammenu_wrapper:color_settings',
                settings: 'index = ammenu_wrapper:settings',
                is_hamburger: 'index = ammenu_wrapper:is_hamburger',
                icons: 'index = ammenu_wrapper:icons',
                is_icons_available: 'index = ammenu_wrapper:is_icons_available',
                mobile_class: 'index = ammenu_wrapper:mobile_class',
                isOpen: 'index = ammenu_hamburger_toggle:isOpen',
                activeTab: 'index = ammenu_tabs_wrapper:activeTab',
                hide_view_all_link: 'index = ammenu_wrapper:hide_view_all_link'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            this._initElems(this.source.data.elems);

            return this;
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    elems: [],
                    isOpen: false,
                    activeTab: 0
                });

            return this;
        },

        /**
         * Toggling button method
         *
         * @param {Object} elem
         * @return {Boolean} for stop or continues propagation
         */
        toggleItem: function (elem) {
            if (elem.isSubmenuVisible && elem.isSubmenuVisible()) {
                elem.isActive(!elem.isActive());
                elem.rendered(true);

                if (elem.isActive()) {
                    elem.color(elem.level()
                        ? this.color_settings.submenu_text_hover
                        : this.color_settings.main_menu_text_hover);
                } else {
                    elem.color(elem.base_color);
                }

                return false;
            }

            return true;
        },

        /**
         * Init Target elements method
         *
         * @param {Object} elems
         * @return {void}
         */
        _initElems: function (elems) {
            var self = this;

            _.each(elems, function (elem) {
                if (elem.elems.length || elem.mobile_content) {
                    self._initElems(elem.elems);
                }

                self._initElem(elem);
            });
        },

        /**
         * Init Target element method
         *
         * @param {Object} elem
         * @return {void}
         */
        _initElem: function (elem) {
            var isMobile = this.source.isMobile();

            if (isMobile) {
                elem.isSubmenuVisible(!elem.hide_mobile_content && (elem.elems.length || elem.mobile_content));
            }

            if (
                isMobile && !this.hide_view_all_link &&
                (elem.elems.length || elem.mobile_content && elem.mobile_content.trim().length > 7
                    && !elem.hide_mobile_content)
            ) {
                helpers.initAllItemLink(elem, this.color_settings.third_level_menu);
            }

            // Disabling focusing on first item in case when content is first element in mobile menu
            if (isMobile && elem.mobile_content && !elem.show_mobile_content && elem.elems.length) {
                elem.elems[0].isFocused = false;
            }
            // END

            if (elem.level() && !elem.current) {
                elem.color(this.color_settings.third_level_menu);
                elem.base_color = this.color_settings.third_level_menu;
            }
        }
    });
});
