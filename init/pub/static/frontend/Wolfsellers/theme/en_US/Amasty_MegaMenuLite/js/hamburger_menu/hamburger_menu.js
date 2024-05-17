/**
 *  Amasty Hamburger Wrapper UI Component
 */

define([
    'ko',
    'underscore',
    'uiComponent',
    'ammenu_helpers'
], function (ko, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            templates: {
                items: 'Amasty_MegaMenuLite/hamburger_menu/items',
                submenu: 'Amasty_MegaMenuLite/submenu/wrapper'
            },
            imports: {
                view_port: 'index = ammenu_wrapper:view_port',
                root_templates: 'index = ammenu_wrapper:templates',
                icons: 'index = ammenu_wrapper:icons',
                color_settings: 'index = ammenu_wrapper:color_settings',
                is_icons_available: 'index = ammenu_wrapper:is_icons_available',
                mobile_class: 'index = ammenu_wrapper:mobile_class',
                isOpen: 'index = ammenu_hamburger_toggle:isOpen',
                activeTab: 'index = ammenu_tabs_wrapper:activeTab'
            }
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            var self = this;

            self._super();

            self.isOpen.subscribe(function (value) {
                if (!value) {
                    self._clearItems();
                }
            });

            self._initElems();

            return self;
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    activeTab: 0,
                    isOpen: false,
                    activeElem: false
                });

            return this;
        },

        /**
         * Toggling button method
         *
         * @param {Object} elem
         * @param {Object} node - element
         * @return {Boolean | void}
         */
        toggleItem: function (elem, node) {
            var opening = !elem.isActive(),
                toggleRect = node.getBoundingClientRect();

            this._clearItems();

            elem.rendered(true);

            if (!opening) {
                return false;
            }

            elem.isActive(true);
            elem.color(this.color_settings.main_menu_text_hover);
            this.activeElem(elem);

            if (_.isUndefined(window.ResizeObserver)) {
                this._setSubmenuPosition(elem, toggleRect);
            } else {
                this._setSubmenuResizeObserver(elem, toggleRect);
            }

            return false;
        },

        /**
         * Menu item hover handler
         *
         * @public
         * @param {Object} elem
         * @return {void}
         */
        onMouseenter: function (elem) {
            elem.color(this.color_settings.main_menu_text_hover);
        },

        /**
         * Menu item mouse leave handler
         *
         * @public
         * @param {Object} elem
         * @return {Boolean|void} for skipping
         */
        onMouseleave: function (elem) {
            if (elem.isActive()) {
                return false;
            }

            elem.color(elem.base_color);
        },

        /**
         * Set current opened submenu position via view port
         * Only for root level
         *
         * @param {Object} elem
         * @param {Object} clickedRect - clicked element
         * @return {Boolean} for propagation
         */
        _setSubmenuPosition: _.debounce(function (elem, clickedRect) {
            var submenuRect,
                inViewport;

            elem.submenu_position.top(clickedRect.top);
            elem.submenu_position.bottom(false);

            submenuRect = elem.nodes.submenu.getBoundingClientRect();
            inViewport = submenuRect.bottom <= this.view_port.height;

            if (!inViewport) {
                elem.submenu_position.top(false);
                elem.submenu_position.bottom(1); // 0 is false
            }
        }),

        /**
         * Set submenu resize observer
         * for changing submenu position provided container node resized
         *
         * @param {Object} elem
         * @param {Object} toggleRect - submenu trigger clicked element
         * @return {void | Boolean}
         */
        _setSubmenuResizeObserver: function (elem, toggleRect) {
            this.submenuResizeObserver = new ResizeObserver(this._setSubmenuPosition.bind(this, elem, toggleRect));
            this.submenuResizeObserver.observe(elem.nodes.submenu);
        },

        /**
         * Remove submenu resize observer
         * Provided menu elem closed
         *
         * @param {Object} elem
         * @return {void}
         */
        _removeSubmenuResizeObserver: function (elem) {
            if (this.submenuResizeObserver) {
                this.submenuResizeObserver.unobserve(elem.nodes.submenu);
            }
        },

        /**
         * Elements init method
         *
         * @return {void}
         */
        _initElems: function () {
            _.each(this.source.data.elems, function (elem) {
                if (elem.is_category) {
                    this._initRoot(elem);
                    this.elems.push(elem);
                }
            }.bind(this));
        },

        /**
         * Init root submenu element
         *
         * @param {Object} elem
         * @return {void}
         */
        _initRoot: function (elem) {
            var self = this;

            elem.isActive.subscribe(function (value) {
                if (!value) {
                    self.onMouseleave(elem);
                }
            });

            elem.submenu_position = {
                top: ko.observable(false),
                bottom: ko.observable(false)
            };
        },

        /**
         * Closing active element
         *
         * @return {void}
         */
        _clearItems: function () {
            var activeElem = this.activeElem();

            if (activeElem) {
                activeElem.isActive(false);
                this.onMouseleave(activeElem);
                this._removeSubmenuResizeObserver(activeElem);
                this.activeElem(false);
            }
        }
    });
});
