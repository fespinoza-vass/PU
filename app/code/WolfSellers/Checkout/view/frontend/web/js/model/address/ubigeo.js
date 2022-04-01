define([
    'jquery',
    'ko',
    'mage/url'
], function (
    $,
    ko,
    url
) {
    'use strict';

    return {
        listUbigeo: ko.observableArray(null),

        /**
         * Load ubigeos.
         *
         * @param regionId
         * @param city
         */
        getUbigeos: function (regionId, city) {
            var self = this;

            if (!city || !regionId) {
                this.listUbigeo(null);

                return;
            }

            $('body').trigger('processStart');

            var payload = {
                'region_id': regionId,
                'city': city
            };

            $.ajax({
                type: 'POST',
                url: url.build('zipcode/index/gettown'),
                dataType: 'json',
                data: payload,
                global: false
            }).done(function (response) {
                response = $.parseJSON(response);
                self.listUbigeo(response);
            }).always(function () {
                $('body').trigger('processStop');
            });
        }
    };
});
