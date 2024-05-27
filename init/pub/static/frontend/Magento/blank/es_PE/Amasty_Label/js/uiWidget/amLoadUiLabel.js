define([
    'jquery',
    'underscore',
    'Amasty_Label/js/configurable/reload',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/product/view/product-ids-resolver',
    'uiRegistry'
], function ($, _, reloader, url, customerData, productResolver, registry) {
    'use strict';

    $.widget('mage.amInitLabelUi', {
        options: {
            config: null,
            productsLifetime: 1000,
            scope: 'group'
        },
        selectors: {
            widgetWrapper: '[data-bind="scope: \'%1.%2\'"]',
            imageContainer: '.block-viewed-products-grid .product-item-info',
            addToCartForm: '#product_addtocart_form'
        },
        modules: {
            recentlyViewed: 'ns = widget_recently_viewed'
        },
        controller: 'amasty_label/ajax/label',

        /**
         * Widget constructor.
         * @protected
         */
        _create: function () {
            this.uiWidget('trigger', true);
        },

        /**
         * @public
         * @return {Object}
         */
        uiWidget: function () {
            return registry.get(this.modules.recentlyViewed, function (component) {
                this.setLabelHtml(component);
            }.bind(this));
        },

        /**
         * @public
         * @return {void}
         */
        setLabelHtml: function (component) {
            var self = this,
                idsData = Object.keys(component.ids()
                    ? component.ids()
                    : this.filterIds(JSON.parse(window.localStorage.getItem('recently_viewed_product')))),
                target = $(this.selectors.widgetWrapper
                    .replace('%1', component.dataScope)
                    .replace('%2', component.ns)),
                isSyncWidgetWithBackend = +component.idsStorage.allowToSendRequest,
                options = {
                    childList: true,
                    subtree: true
                },
                observer;

            if (!idsData.length && !isSyncWidgetWithBackend) {
                return;
            }

            observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList'
                        && mutation.previousSibling
                        && typeof mutation.previousSibling.dataset !== 'undefined'
                        && typeof mutation.previousSibling.dataset.post !== 'undefined'
                    ) {
                        reloader.reload(
                            self.selectors.imageContainer,
                            isSyncWidgetWithBackend ? Object.keys(component.ids()) : idsData,
                            url.build(self.controller),
                            true
                        );

                        observer.disconnect();
                    }
                });
            });

            observer.observe(target[0], options);
        },

        /**
         * @param ids
         * @public
         * @return {Object}
         */
        filterIds: function (ids) {
            var result = {},
                lifetime = this.options.productsLifetime,
                currentTime = new Date().getTime() / 1000,
                currentProductIds = productResolver($(this.selectors.addToCartForm)),
                productCurrentScope = this.options.scope,
                scopeId = productCurrentScope === 'store' ? window.checkout.storeId :
                    productCurrentScope === 'group' ? window.checkout.storeGroupId :
                        window.checkout.websiteId;

            _.each(ids, function (id, key) {
                if (
                    currentTime - ids[key]['added_at'] < lifetime &&
                    !_.contains(currentProductIds, ids[key]['product_id']) &&
                    (!id.hasOwnProperty('scope_id') || ids[key]['scope_id'] === scopeId)
                ) {
                    result[id['product_id']] = id;
                }
            }, this);

            return result;
        }
    });

    return $.mage.amInitLabelUi;
});
