define([
    'jquery',
    'Amasty_PromoBanners/js/injector',
    'catalogAddToCart'
], function ($, bannerInjector) {
    'use strict';
    var options = {
            context: []
        },
        banner = {
            data: {},
            selectors: {
                BANNER_SELECTOR: '.ambanners.ambanner-'
            },

            init: function () {
                this.data = options;
                this.insertBanners();
                this.injectBanners();
                $("[data-role=amasty-banner-tocart-form]").catalogAddToCart({});
            },

            getSectionBanners: function (section) {
                if (!(section in this.data.sections)) {
                    return [];
                }

                return this.data.sections[section].map(function (id) {

                    return this.data.content[id];
                }.bind(this));
            },
            getBanners: function (bannerId) {
                if (this.data.banners.indexOf(bannerId) === -1) {
                    return [];
                }
                var insertedBanner = this.data.banners.find(function (element) {
                    return element === bannerId;
                }.bind(this));

                return this.data.content[insertedBanner];
            },
            insertBanners: function () {
                var self = this;

                $('[data-role="amasty-banner-container"]').each(function () {
                    var sectionId = $(this).data('position');

                    if (typeof sectionId === "number") {
                        $(this).html(self.getSectionBanners(sectionId).join(''));
                        if (sectionId === 15) {
                            $('.product-item-inner').append($(self.selectors.BANNER_SELECTOR + sectionId));
                            $(self.selectors.BANNER_SELECTOR + sectionId).show();
                        }
                    } else {
                        var bannerId = $(this).data('bannerid');
                        $(this).html(self.getBanners(bannerId));
                    }

                    self.addProductSidebarClass();
                });
            },

            addProductSidebarClass: function () {
                var sidebarPositions = [1, 2];
                $.each(sidebarPositions, function (index, value) {
                    var positionSelector = '[data-position="' + value + '"]';
                    $(positionSelector).find('li, a.product.photo.product-item-photo, .product.details.product-item-details.product-item-details').addClass('side-banner');
                });
            },

            injectBanners: function () {
                var container = $('[data-role="amasty-banner-container"][data-position='
                    + options.injectorSectionId + ']');

                if (container.length == 0) {
                    return;
                }

                Object.keys(this.data.injectorParams.banners).map(function (id, index) {
                    var params = this.data.injectorParams.banners[id];

                    bannerInjector({
                        containerSelector: this.data.injectorParams.containerSelector,
                        itemSelector: this.data.injectorParams.itemSelector,
                        afterProductRow: params.afterProductRow,
                        afterProductNum: params.afterProductNum,
                        width: params.width}
                    ).inject(container.find('[data-banner-id=' + id + ']')[0]);
                }.bind(this));
            },

            'Amasty_PromoBanners/js/loader': function (settings) {
                options = $.extend(options, settings);
                banner.init();
            }
        };

    return banner;
});
