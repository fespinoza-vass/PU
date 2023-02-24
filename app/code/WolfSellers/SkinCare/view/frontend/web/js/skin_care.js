/**
 * Skin care.
 */
define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    'use strict';

    $.widget('wolfsellers.skinCare', {
        btnOpen: null,
        recommendations: null,
        result: null,
        resultItems: null,
        resultItemsBar: null,
        resultElements: {},
        resultSliders: null,
        formId:null,

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
            productsSliderSelector: '.product-items',
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
                    resultType = $this.data('type'),
                    $sliderWrapper = $('.slider-' + resultType)
                ;

                self.resultElements[resultType] = {
                    el: $this,
                    wrapper: $this.closest(self.options.resultItemSelector),
                    percentage: $this.find('.percentage'),
                    bar: $this.find('.bar-percentage'),
                    slider: $sliderWrapper,
                    productsSlider: $sliderWrapper.find(self.options.productsSliderSelector)
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

            this.formId = utils.uniqueid(15);
            self._setFormId();

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

            handlers['click .test-it'] = '_testIt';

            this._on(handlers);
        },

        _testIt:function (e) {
            var self = this;
            var report = {ageSpots: 74, darkCircles: 77, texture: 65, wrinkles: 86, skinAge: 27, skinHealth:76, timed:2372};
            console.log('Cargando información de prueba....');
            self._onAnalysisUpdated(report);
            console.log('Espera que los 4 servicios respondan...');
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
            this.resultSliders.show();
            this.result.show();

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
                elements.productsSlider.slick('refresh');
            });
        },

        _ajaxSkinCareCall: function (type, value) {
            var self = this;
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
                    typeKey = "";
                    break;
                }

            }
            if (typeKey !== "") {
                $.get(
                    window.BASE_URL + "skincare/index/index?value=" + value + "&type=" + typeKey + "&form=" + self.formId,
                    {},
                    function(data) {
                        var $container = $("#" + typeKey + "-container");
                        var $parentContainer = $("." + typeKey + "-parent-container");
                        $parentContainer.hide();
                        console.log(typeKey);

                        if (data !== "") {
                            $container.html(data);
                            $parentContainer.show();
                            $('body').trigger('click');
                            $parentContainer.click();
                        }
                    }
                );
            }
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
        },

        _setFormId: function (){
            $("#textinput-formid").attr("type", "hidden").val(this.formId);
        }
    });

    return $.wolfsellers.skinCare;
});
