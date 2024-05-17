/**
 *  Amasty Submenu Builder UI Component
 */

define([
    'uiComponent',
    'knockout',
    'underscore',
    'ammenu_helpers'
], function (Component, ko, _, helpers) {
    'use strict';

    return Component.extend({
        defaults: {
            hoverTimeout: 350,
            activeElem: false,
            drawTimeOut: null,
            template: 'Amasty_MegaMenu/submenu/builder/wrapper',
            templates: {
                itemsList: 'Amasty_MegaMenu/submenu/builder/items_list',
                itemWrapper: 'Amasty_MegaMenu/submenu/builder/item_wrapper',
                contentBlock: 'Amasty_MegaMenu/submenu/builder/content_block'
            },
            imports: {
                color_settings: 'index = ammenu_wrapper:color_settings',
                is_icons_available: 'index = ammenu_wrapper:is_icons_available',
                hide_view_all_link: 'index = ammenu_wrapper:hide_view_all_link',
                root_templates: 'index = ammenu_wrapper:templates',
                animation_time: 'index = ammenu_wrapper:animation_time'
            }
        },
        selectors: {
            slick: '.slick-initialized'
        },

        /**
         * Init root submenu element
         *
         * @public
         * @param {Object} elem
         * @return {void}
         */
        initRoot: function (elem) {
            var self = this;

            self._setCurrentElement(elem);

            elem.isActive.subscribe(function (value) {
                if (value) {
                    self._setCurrentElement(elem);
                }
            });

            self._initElems(elem);
        },

        /**
         * @private
         * @param {Object} item
         * @return {void}
         */
        _setCurrentElement: function (item) {
            item.isContentActive(true);
            this.activeElem = item;
        },

        /**
         * Content Block Init
         *
         * @param {Object} node - content node
         * @param {Object} context - target context
         *
         * @desc Start method after render content block
         * @return {void}
         */
        initContent: function (node, context) {
            helpers.applyBindings(node, context);
            helpers.sliderResizeSubscribe(node, context.elem.isActive);
            helpers.updateFormKey(node);
        },

        /**
         * Menu item hover handler
         *
         * @public
         * @param {Object} elem
         * @return {void}
         */
        onMouseenter: function (elem) {
            elem.color(this.color_settings.submenu_text_hover);
            this._setActiveItem(elem);
        },

        /**
         * Menu item mouse leave handler
         *
         * @public
         * @param {Object} elem
         * @return {void}
         */
        onMouseleave: function (elem) {
            elem.color(elem.base_color);
        },

        /**
         * Reset target submenu to default state
         *
         * @param {Object} item target submenu
         * @return {void}
         */
        reset: function (item) {
            var self = this;

            self.clearHoverTimeout();
            self.setParentsTreeState(self.activeElem, false);
            item.isContentActive(true);
            self.activeElem = item;
        },

        /**
         * Set Active State for each items up the tree
         *
         * @param {Object} item
         * @param {Boolean} itemState
         * @return {void | Boolean}
         */ // eslint-disable-next-line consistent-return
        setParentsTreeState: function (item, itemState) {
            if (!item || !item.level() || _.isUndefined(item.isActive)) {
                return false;
            }

            item.isActive(itemState);
            this.setParentsTreeState(item.parent, itemState);
        },

        /**
         * Clearing hover effect interval
         *
         * @return {Boolean} - status
         */ // eslint-disable-next-line consistent-return
        clearHoverTimeout: function () {
            if (this.drawTimeOut) {
                clearInterval(this.drawTimeOut);

                this.drawTimeOut = null;

                return true;
            }
        },

        /**
         * Set current item to active state with delay
         *
         * @private
         * @param {Object} item
         * @return {void}
         */
        _setActiveItem: function (item) {
            var self = this;

            if (_.isUndefined(item.isActive) || item.isActive() && item.isContentActive()) {
                return;
            }

            self.clearHoverTimeout();

            self.drawTimeOut = setTimeout(function () {
                if (self.activeElem) {
                    self.setParentsTreeState(self.activeElem, false);
                    self.activeElem.isContentActive(false);
                }

                self.setParentsTreeState(item, true);
                item.isContentActive(true);
                self.activeElem = item;
                helpers.setItemFocus(self.activeElem);
            }, self.hoverTimeout);
        },

        /**
         * Init Target elements method
         *
         * @private
         * @param {Object} element
         * @return {void}
         */
        _initElems: function (element) {
            var self = this;

            _.each(element.elems, function (elem) {
                self._initElem(elem);

                if (elem.elems && elem.elems.length) {
                    self._initElems(elem);
                }
            });

            if (element.elems.length && !this.hide_view_all_link) {
                helpers.initAllItemLink(
                    element,
                    element.level() === 0 ? this.color_settings.submenu_text : this.color_settings.third_level_menu
                );
            }
        },

        /**
         * Init Target element method
         *
         * @param {Object} elem
         * @return {void}
         */
        _initElem: function (elem) {
            elem.isLinkInteractive = !_.isUndefined(elem.isActive) && !_.isUndefined(elem.isContentActive) &&
                elem.content && elem.content.trim().length > 7 && !elem.hide_content ||
                !!elem.elems.length && elem.type && elem.type.value && !elem.hide_content ||
                !!elem.url.length;

            elem.isContentInteractive = !elem.hide_content &&
                (elem.type.value || elem.content && elem.content.trim().length > 7);

            if (elem.type) {
                elem.type.label = elem.type.label.split(' ').join('_');
            }

            if (elem.level() === 1 && !elem.current) {
                elem.color(this.color_settings.submenu_text);
                elem.base_color = this.color_settings.submenu_text;
            }

            if (elem.level() > 1 && !elem.current) {
                elem.color(this.color_settings.third_level_menu);
                elem.base_color = this.color_settings.third_level_menu;
            }
        }
    });
});
