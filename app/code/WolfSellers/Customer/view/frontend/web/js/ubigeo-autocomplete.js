/**
 * Ubigeo address.
 */
define([
    'jquery',
    'underscore',
    'mage/url'
], function ($, _, url) {
    'use strict';

    $.widget('wolfsellers.ubigeoAutocomplete', {
        region: null,
        city: null,
        town: null,
        zip: null,

        options: {
            defaultRegion: null,
            defaultCity: null,
            defaultTown: null,

            regionId: '#region_id',
            cityId: '#city',
            townId: '#colony',
            zipId: '#zip'
        },

        /**
         * @private
         */
        _create: function () {
            this.region = this.element.find(this.options.regionId);
            this.city = this.element.find(this.options.cityId);
            this.town = this.element.find(this.options.townId);
            this.zip = this.element.find(this.options.zipId);

            this._bind();

            url.setBaseUrl(window.BASE_URL);

            if (this.options.defaultRegion) {
                this._loadCities(this.options.defaultRegion, this.options.defaultCity);
            }

            if (this.options.defaultCity) {
                this._loadTowns(this.options.defaultRegion, this.options.defaultCity, this.options.defaultTown);
            }
        },

        _bind: function () {
            var handlers = {};

            handlers['change ' + this.options.regionId] = '_onRegionChange';
            handlers['change ' + this.options.cityId] = '_onCityChange';
            handlers['change ' + this.options.townId] = '_onTownChange';

            this._on(handlers);
        },

        _onRegionChange: function () {
            var regionValue = this.region.val();

            this.city.find('option:not(:first)').remove();
            this.town.find('option:not(:first)').remove();
            this.zip.val('');

            if (!regionValue) {
                return false;
            }

            this._loadCities(regionValue);
        },

        _onCityChange: function () {
            var regionValue = this.region.val(),
                cityValue = this.city.val()
            ;

            this.town.find('option:not(:first)').remove();
            this.zip.val('');

            if (!cityValue) {
                return false;
            }

            this._loadTowns(regionValue, cityValue);
        },

        _onTownChange: function () {
            var postcode = '';

            if (this.town.val()) {
                postcode = this.town.find('option:selected').data('postcode');
            }

            this.zip.val(postcode);
        },

        _loadCities: function (regionId, defaultValue) {
            var self = this;

            $('body').trigger('processStart');

            var payload = {
                'region_id': regionId
            };

            $.ajax({
                type: 'POST',
                url: url.build('zipcode/index/getcity'),
                dataType: 'json',
                data: payload,
                global: false
            }).done(function (response) {
                response = $.parseJSON(response);

                $.each(response, function (index, item) {
                    var selected = item.value === defaultValue;

                    self.city.append(new Option(item.label, item.value, selected, selected));
                });
            }).always(function () {
                $('body').trigger('processStop');
            });
        },

        _loadTowns: function (regionId, city, defaultValue) {
            var self = this;

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

                $.each(response, function (index, item) {
                    var selected = item.value === defaultValue;

                    self.town.append(
                        $('<option>')
                            .val(item.value)
                            .text(item.label)
                            .data('postcode', item.postcode)
                            .prop('selected', selected)
                    );
                });
            }).always(function () {
                $('body').trigger('processStop');
            });
        },
    });

    return $.wolfsellers.ubigeoAutocomplete;
});
