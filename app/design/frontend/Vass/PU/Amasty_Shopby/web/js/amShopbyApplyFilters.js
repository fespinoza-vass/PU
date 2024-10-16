/**
 * @copyright Copyright (c) 2024 VASS
 * @author VASS Team
 */

define([
    "underscore",
    "jquery",
    "amShopbyFilterAbstract",
    "mage/translate"
], function (_, $) {
    'use strict';

    $.widget('mage.amShopbyApplyFilters', {
        showButtonClick: false,
        showButtonContainer: '.am_shopby_apply_filters',
        showButton: 'am-show-button',

        _create: function () {
            var self = this;
            $(function () {
                var element = $(self.element[0]);
                $(self.showButtonContainer).appendTo('.block-content.filter-content');

                element.on('click', function (e) {
                    $.mage.amShopbyFilterAbstract.prototype.options.isCategorySingleSelect
                        = self.options.isCategorySingleSelect;

                    window.onpopstate = function () {
                        location.reload();
                    };

                    if (self.options.ajaxSettingEnabled !== 1) {
                        document.location.href = $.mage.amShopbyApplyFilters.prototype.responseUrl;
                    } else {
                        let {ajaxData, clearFilter, isSorting} = $.mage.amShopbyAjax.prototype.prevData;

                        ajaxData.isGetCounter = false;

                        $(element).trigger('amshopby:submit_filters', {
                            data: ajaxData,
                            clearFilter: clearFilter,
                            isSorting: isSorting
                        });
                    }

                    this.blur();
                    //self.removeShowButton();

                    return true;
                });
            });
        },

        renderShowButton: function (e, element) {
        },

        removeShowButton: function () {
            $($.mage.amShopbyApplyFilters.prototype.showButtonContainer).remove();
        },

        showButtonCounter: function (count) {
            var items = $('.' + $.mage.amShopbyApplyFilters.prototype.showButton + ' .am-items'),
                button = $('.' + $.mage.amShopbyApplyFilters.prototype.showButton + ' .amshopby-button');

            items.removeClass('-loading');

            count = parseInt(count);

            if (count > 1) {
                items.html(count + ' ' + $.mage.__('Items'));
                button.prop('disabled', false);
            } else if (count === 1) {
                items.html(count + ' ' + $.mage.__('Item'));
                button.prop('disabled', false);
            } else {
                items.html(count + ' ' + $.mage.__('Items'));
                button.prop('disabled', true);
            }
        },
    });

    return $.mage.amShopbyApplyFilters;
});
