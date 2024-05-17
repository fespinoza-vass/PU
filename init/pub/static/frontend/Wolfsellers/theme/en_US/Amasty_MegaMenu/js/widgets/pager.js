define([
    'jquery'
], function ($) {
    $.widget('ammenu.Pager', {
        options: {},

        /**
         * @inheritDoc
         */
        _create: function () {
            $(this.element).find('.pager').on('click', '.item:not(.current) a, a.action', function (e) {
                var target = $(e.target);

                e.preventDefault();
                e.stopPropagation();

                if (!target.is('a')) {
                    target = target.parents('a');
                }

                this._getPageData(target[0].href, this._setPageData.bind(this));

                return false;
            }.bind(this));

            return this;
        },

        /**
         * Set Target Page data
         *
         * @param {String} data target html
         */
        _setPageData: function (data) {
            var widget_data = this.options.widget_data,
                element = $('[data-ammenu-js=\'' + widget_data['identifier'] + '\']');

            element.html(data);
            element.trigger('contentUpdated');
        },

        /**
         * Get Target Page data
         *
         * @param {String} pageUrl target url
         * @param {Object} callback
         * @return {Object|Boolean} pageData
         */
        _getPageData: function (pageUrl, callback) {
            var widget_data = this.options.widget_data;

            $.ajax({
                url: pageUrl,
                data: { 'widget_data': widget_data },
                type: 'get',
                success: function (response) {
                    if (response['block'] !== undefined) {
                        return callback($(response['block']).html());
                    } else {
                        return false;
                    }
                }
            });
        }
    });

    return $.ammenu.Pager;
});
