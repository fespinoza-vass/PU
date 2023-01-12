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
	                $('.slider-'+key+' .product-items .slick-list .slick-track .slick-slide div .product-item .product-item-info').each(function(){
	                    var max = $(this).attr('data-'+key+'-max');
	                    var min = $(this).attr('data-'+key+'-min');

                    	//console.log(valReport + '<' + max + '||' + valReport + '>' + min);
                    	if (parseFloat(valReport) >= min && parseFloat(valReport) <= max) {
	                    	console.log('SE MUESTRA');
	                    	$(this).parent().parent().parent().show();
	                    }else{
	                    	console.log('SE OCULTA');
	                    	$(this).parent().parent().parent().hide();
	                    }
	                });
                }
                
                elements.slider.show();
                elements.productsSlider.slick('refresh');
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
