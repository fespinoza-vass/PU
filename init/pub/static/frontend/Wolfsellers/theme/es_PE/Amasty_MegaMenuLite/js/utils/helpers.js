/**
 * Amasty MegaMenu helpers
 */

define([
    'jquery',
    'ko',
    'underscore',
    'uiLayout',
    'mage/cookies'
], function ($, ko, _, layout) {
    'use strict';

    return {
        selectors: {
            formKeyInput: 'input[name="form_key"]',
            slick: '.slick-slider',
            slide: '.slick-slide'
        },
        formKey: $.mage.cookies.get('form_key'),

        /**
         * Update Form Key
         *
         * @param {Object} node
         *
         * @desc Updating inner form key inserting
         * @return {void}
         */
        updateFormKey: function (node) {
            var self = this,
                formKeyInput;

            _.delay(function () {
                formKeyInput = $(node).find(self.selectors.formKeyInput);

                if (formKeyInput.val() !== self.formKey) {
                    formKeyInput.val(self.formKey);
                }
            });
        },

        /**
         * Mounting necessary components into layout via enable_condition
         *
         * @description please specify "uiClass" environment for enable_condition params & select parent
         * @param {Object} uiClass
         * @return {Boolean}
         */
        mountComponents: function (uiClass) {
            Object.keys(uiClass.components).forEach(function (key) {
                uiClass.components[key].parent = uiClass.name;

                if (_.isUndefined(uiClass.components[key].enable_condition)) {
                    layout([uiClass.components[key]]);

                    return false;
                }

                if (uiClass.components[key].enable_condition.apply(uiClass)) {
                    layout([uiClass.components[key]]);
                }
            });

            return true;
        },

        /**
         * ReMounting necessary and Destroy unnecessary components into/to layout via enable_condition
         *
         * @description please specify "uiClass" environment for enable_condition params & select parent
         * @params {Object} uiClass
         * @return {void}
         */
        remountComponents: function (uiClass) {
            uiClass.elems.each(function (component) {
                if (!component.enable_condition.call(uiClass)) {
                    component.destroy();
                }
            });

            this.mountComponents(uiClass);
        },

        /**
         * Applying Bindings in target node element
         *
         * @param {Object} element - node element
         * @param {Object} context - current context
         * @return {void}
         */
        applyBindings: function (element, context) {
            _.defer(function () {
                ko.applyBindingsToDescendants(context, element);
                $(element).trigger('contentUpdated');
            });
        },

        /**
         * Components Array initialization and setting in target component
         *
         * @param {Array} array target uiClasses
         * @param {Object} component current uiClass
         * @return {void}
         */
        initComponentsArray: function (array, component) {
            _.each(array, function (item) {
                component[item.index] = item;
            });
        },

        /**
         * Slick Slider Position checking via subscriber
         *
         * @desc checking and fixing new slick sliders positions
         * @param {Object} node - slider container node
         * @param {Object} observer - ko observer
         * @return {void | Boolean}
         */
        sliderResizeSubscribe: function (node, observer) {
            var self = this,
                $slider,
                $slide,
                sliderSpeed,
                sliderAutoplay,
                subscriber = observer.subscribe(function (value) {
                    if (!value) {
                        return false;
                    }

                    $slider = $(node).find(self.selectors.slick);

                    if (!$slider.length) {
                        subscriber.dispose();

                        return false;
                    }

                    sliderAutoplay = $slider.slick('slickGetOption', 'autoplay');
                    sliderSpeed = $slider.slick('slickGetOption', 'speed');
                    $slide = $slider.find(self.selectors.slide).first();

                    if (!parseInt(sliderAutoplay, 10) && $slide.width() && $slider.width()) {
                        subscriber.dispose();

                        return false;
                    }

                    $slider.slick('slickSetOption', 'speed', 0);
                    $slider.slick('slickGoTo', 0);
                    $slider.slick('setPosition');
                    $slider.slick('setDimensions');
                    $slider.slick('slickSetOption', 'speed', sliderSpeed);
                });
        },

        /**
         * Set focus on first element in target
         *
         * @public
         * @param {Object} elem
         * @return {void}
         */
        setItemFocus: _.debounce(function (elem) {
            if (elem && elem.elems.length && _.isFunction(elem.elems[0].isFocused)) {
                elem.elems[0].isFocused(true);
            }
        }, 500),

        /**
         * All category link element generator
         *
         * @desc preparing object element for 'all category' link via current data
         * and shifting to current Array
         * @param {Object} elem - slider container node
         * @param {String} color - target color
         * @return {Boolean} status
         */
        initAllItemLink: function (elem, color) {
            var prototype;

            if (
                !elem.url.length ||
                elem.elems.length && elem.elems[0].isViewAll ||
                !_.isUndefined(elem.all_link) && !elem.all_link
            ) {
                return false;
            }

            prototype = {
                name: $.mage.__('View All') + ' ' + elem.name,
                isLinkInteractive: true,
                index: 0,
                isFocused: ko.observable(false),
                isViewAll: true,
                url: elem.url,
                elems: [],
                content: '',
                hide_content: true,
                isSubmenuVisible: ko.observable(false),
                type: elem.type,
                additionalClasses: ' -all-link',
                color: ko.observable(color),
                base_color: color,
                isVisible: ko.observable(true),
                level: ko.observable(elem.level()),
                parent: elem
            };

            elem.elems.unshift(prototype);

            return true;
        }
    };
});
