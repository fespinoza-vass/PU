/**
 * Slick Slider Initialization
 *
 * @desc Slick slider init and generating options
 */

define([
    'jquery',
    'uiRegistry',
    'ammenu_helpers',
    'Amasty_Base/vendor/slick/slick.min'
], function ($, registry, helpers) {
    'use strict';

    $.widget('ammenu.ProductSlider', {
        components: [
            'index = ammenu_wrapper'
        ],
        selectors: {
            slickInit: '.slick-initialized'
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            registry.get(this.components, function () {
                var $wrapper = $(this.element);

                helpers.initComponentsArray(arguments, this);

                if (this.ammenu_wrapper.isMobile()) {
                    this.options = {
                        infinite: true,
                        autoplay: false,
                        dots: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false
                    };
                } else {
                    $wrapper.width(this.options.width);
                }

                $wrapper.not(this.selectors.slickInit).slick(this.options);
            }.bind(this));

            return this;
        }
    });

    return $.ammenu.ProductSlider;
});
