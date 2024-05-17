/**
 *  Amasty Top Menu Item elements UI Component
 */

define([
    'ko',
    'uiComponent',
    'uiRegistry',
    'underscore',
    'ammenu_helpers'
], function (ko, Component, registry, _, helpers) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                root_templates: 'index = ammenu_wrapper:templates',
                color_settings: 'index = ammenu_wrapper:color_settings',
                view_port: 'index = ammenu_wrapper:view_port',
                settings: 'index = ammenu_wrapper:settings'
            },
            components: [
                'index = ammenu_wrapper'
            ]
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            registry.get(this.components, function () {
                helpers.initComponentsArray(arguments, this);
                this.item = this._getElemById(this.id);
                this._initItem(this.item);
            }.bind(this));

            return this;
        },

        /**
         * Init submenu
         * @public
         * @returns {void}
         */
        _initSubmenu: function () {
            this.item.inited = true;

            if (this.item.nodes.submenu && this.item.width) {
                if (_.isUndefined(window.ResizeObserver)) {
                    this._setSubmenuPosition(this.item.nodes.submenu);
                } else {
                    this._setSubmenuResizeObserver(this.item.nodes.submenu);
                }
            }
        },

        /**
         * Menu item hover handler
         *
         * @public
         * @return {void}
         */
        onMouseenter: function () {
            if (this.ammenu_wrapper.topMenuActiveItem()) {
                this.ammenu_wrapper.topMenuActiveItem().isActive(false);
            }

            this.ammenu_wrapper.topMenuActiveItem(this.item);
            this.item.isActive(true);
            this.item.rendered(true);
        },

        /**
         * Menu item mouse leave handler
         *
         * @public
         * @return {void}
         */
        onMouseleave: function () {
            this.ammenu_wrapper.topMenuActiveItem(null);
            this.item.isActive(false);
        },

        /**
         * Find target elem by id in source data
         *
         * @private
         * @returns {Object} targetElem
         */
        _getElemById: function () {
            return this.ammenu_wrapper.data.elems[this.ammenu_wrapper.maps.id_index[this.id]];
        },

        /**
         * Set submenu resize observer
         * for changing submenu position provided container node resized
         *
         * @param {Object} node
         * @return {void}
         */
        _setSubmenuResizeObserver: function (node) {
            this.submenuResizeObserver = new ResizeObserver(this._setSubmenuPosition.bind(this, node));
            this.submenuResizeObserver.observe(node);
        },

        /**
         * Set current opened submenu position via view port
         * Only for root level
         *
         * @params {Object} node
         * @return {Boolean} for propagation
         */ // eslint-disable-next-line consistent-return
        _setSubmenuPosition: _.debounce(function (node) {
            var submenuRect,
                inViewPort,
                isCustomWidth = this.item.width === 2;

            if (this.item.isActive() && this.item.submenu_position.left()) {
                return false;
            }

            this.item.width_value(isCustomWidth ? this.item.width_value() : 'max-content');

            this.item.submenu_position.left(false);

            submenuRect = node.getBoundingClientRect();
            inViewPort = submenuRect.right <= this.view_port.width;

            if (!inViewPort) {
                this.item.width_value(isCustomWidth ? this.item.width_value() : '100%');
                this.item.submenu_position.left(1); // 0 is false
            }
        }, 300),

        /**
         * Init Target item
         *
         * @private
         * @param {Object} item
         * @return {void}
         */
        _initItem: function (item) {
            item.isInteractive = !item.hide_content
                && (!!item.elems.length || item.content && !!item.content.length)
                || !!item.url.length;
            item.backgroundColor = ko.observable('');
            item.isActive.extend({ rateLimit: this.settings.delay });

            if (!item.isInteractive) {
                return;
            }

            item.submenu_position = {
                left: ko.observable(false)
            };
            item.isActive.subscribe(function (value) {
                if (value) {
                    item.backgroundColor(this.color_settings.main_menu_background_hover);
                    item.color(this.color_settings.main_menu_text_hover);
                } else {
                    item.backgroundColor('');
                    item.color(item.base_color);
                }

                if (!item.inited) {
                    this._initSubmenu();
                }
            }.bind(this));
        }
    });
});
