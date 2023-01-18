/**
 * Skin care.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('wolfsellers.skinCare', {
        btnOpen: null,
        recommendations: null,
        result: null,
        resultItems: null,
        resultItemsBar: null,
        resultElements: {},
        resultSliders: null,

        options: {
            ymk: null,
            width: 0,
            height: 0,
            btnOpenSelector: '.open-skincare',
            recommendationsSelector: '.skin-care-recommendations',
            resultSelector: '.skin-care-results',
            resultItemBarSelector: '.skin-care-bar',
            resultItemSelector: '.result-item',
            resultSliderSelector: '.result-slider',
            limits: {
                ageSpots: 0,
                darkCircles: 0,
                skinAge: 0,
                skinHealth: 0,
                texture: 0,
                wrinkles: 0
            }
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;

            this.btnOpen = this.element.find(this.options.btnOpenSelector);
            this.recommendations = $(this.options.recommendationsSelector);
            this.result = $(this.options.resultSelector);
            this.resultItemsBar = $(this.options.resultItemBarSelector);

            $(this.resultItemsBar).each(function (index) {
                var $this = $(this),
                    resultType = $this.data('type')
                ;

                self.resultElements[resultType] = {
                    el: $this,
                    wrapper: $this.closest(self.options.resultItemSelector),
                    percentage: $this.find('.percentage'),
                    bar: $this.find('.bar-percentage'),
                    slider: $('.slider-' + resultType),
                };
            });

            this.resultItems = this.resultItemsBar.closest(this.options.resultItemSelector);
            this.resultSliders = $(this.options.resultSliderSelector);

            this.result.hide();
            this.resultSliders.hide();

            this.options.ymk.init({
                autoOpen: false,
                language: 'esp',
                width: this.options.width,
                height: this.options.height,
                hideSkinAnalysisResult: true
            });

            this._bind();
        },

        _bind: function () {
            var handlers = {},
                self = this;

            this.options.ymk.addEventListener('skinAnalysisUpdated', function (report) {
                self._onAnalysisUpdated(report);
            });

            this.options.ymk.addEventListener('opened', function () {
                self._onOpenedSkinCare();
            });

            this.options.ymk.addEventListener('closed', function () {
                self._onClosedSkinCare();
            });

            handlers['click ' + this.options.btnOpenSelector] = '_onOpenSkinCare';

            this._on(handlers);
        },

        _onOpenSkinCare: function (e) {
            e.stopPropagation();

            $('body').trigger('processStart');
            this.options.ymk.openSkincare();
        },

        _onAnalysisUpdated: function (report) {
            var self = this;
            this.recommendations.hide();

            this.resultItems.hide();
            this.resultSliders.hide();

            $.each(this.resultElements, function (key, elements) {
                var valReport = report[key];

                if (valReport > self.options.limits[key]) {
                    return;
                }

                elements.percentage.text(valReport);
                elements.bar.width(valReport + '%');
                elements.wrapper.show();

                if(key == 'ageSpots' || key == 'darkCircles' || key == 'texture' || key == 'wrinkles'){
	                self._ajaxSkinCareCall(key, parseFloat(valReport));
                }

                elements.slider.show();
            });

            this.result.show();
        },

        _ajaxSkinCareCall: function (type, value) {
            var typeKey = type;
            switch (type) {
                case "ageSpots": {
                    typeKey = "spot";
                    break;
                }
                case "texture": {
                    typeKey = "texture";
                    break;
                }
                case "wrinkles": {
                    typeKey = "wrinkle";
                    break;
                }
                case "darkCircles": {
                    typeKey = "dark_circle";
                    break;
                }

                default: {
                    typeKey = "dark_circle";
                    break;
                }

            }
            $.get(
                window.BASE_URL + "skincare/index/index?value=" + value + "&type=" + typeKey,
                {},
                function(data) {
                var $container = $("#" + typeKey + "-container");
                $container.html(data);
                $container.parent().parent().show();
            });
        },

        _onOpenedSkinCare: function () {
            $('body').trigger('processStop');
            this.btnOpen.hide();
        },

        _onClosedSkinCare: function () {
            this.btnOpen.show();
            this.resultItems.hide();
            this.resultSliders.hide();
            this.recommendations.show();
        }
    });

    return $.wolfsellers.skinCare;
});
