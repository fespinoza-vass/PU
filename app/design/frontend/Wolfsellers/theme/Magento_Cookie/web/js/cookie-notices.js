define([
    'jquery',
    'jquery-ui-modules/widget',
    'mage/cookies',
    'Magento_Cookie/js/notices'
], function ($) {
    'use strict';

    $.widget('wolfsellers.cookieNotices', $.mage.cookieNotices, {
        _create: function () {
            this._super();

            this.element.find(this.options.cookieCloseButtonSelector).on('click', $.proxy(function () {
                this.element.hide();
            }, this));
        }
    });

    return $.wolfsellers.cookieNotices;
});
