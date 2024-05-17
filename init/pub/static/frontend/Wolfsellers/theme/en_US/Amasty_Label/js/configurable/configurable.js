define([
    'jquery',
    'underscore',
    'Amasty_Label/js/configurable/reload',
    'Magento_Ui/js/modal/modal'
], function ($, _, reloader) {
    'use strict';

    return function (widget) {
        $.widget('mage.configurable', widget, {
            /**
             * Trigger label reload
             *
             * @return {void}
             */
            _processLabelReload: function () {
                var productId = this.simpleProduct,
                    imageContainer = null,
                    originalProductId = this.options.spConfig['original_product_id'];

                if (this.inProductList) {
                    imageContainer = this.element.closest('li.item').find(this.options.spConfig['label_category']);
                } else {
                    imageContainer = this.element.closest('.column.main').find(this.options.spConfig['label_product']);
                }

                if (!productId) {
                    productId = this.options.spConfig['original_product_id'];
                }

                if (typeof this.options.spConfig['label_reload'] != 'undefined') {
                    reloader.reload(
                        imageContainer,
                        productId,
                        this.options.spConfig['label_reload'],
                        this.inProductList ? 1 : 0,
                        originalProductId
                    );
                }
            },

            /**
             * OVERRIDE
             *
             * @inheritDoc
             */
            _changeProductImage: function (noLabel) {
                if (noLabel !== true) {
                    this._processLabelReload();
                }

                var images,
                    initialImages = this.options.mediaGalleryInitial,
                    gallery = $(this.options.mediaGallerySelector).data('gallery');

                if (_.isUndefined(gallery)) {
                    $(this.options.mediaGallerySelector).on('gallery:loaded', function () {
                        this._changeProductImage(true); // skip label reloading to prevent duplicates
                    }.bind(this));

                    return;
                }

                images = this.options.spConfig.images[this.simpleProduct];

                if (images) {
                    images = this._sortImages(images);

                    if (this.options.gallerySwitchStrategy === 'prepend') {
                        images = images.concat(initialImages);
                    }

                    images = $.extend(true, [], images);
                    images = this._setImageIndex(images);

                    gallery.updateData(images);
                    this._addFotoramaVideoEvents(false);
                } else {
                    gallery.updateData(initialImages);
                    this._addFotoramaVideoEvents(true);
                }
            }
        });

        return $.mage.configurable;
    };
});
