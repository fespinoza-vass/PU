/**
 * Fast shipping.
 */
define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Magento_Ui/js/modal/modal'
], function ($, $t, url, modal) {
    'use strict';

    $.widget('wolfsellers.fastShipping', {
        form: null,
        modal: null,
        region: null,
        city: null,
        town: null,

        options: {
            ubigeo: null,
            calculateButton: '.calculate-button',
            recalculateButton: '.recalculate-button',
            formSelector: '#fast-shipping-form',
            modalSelector: '#modal-estimate-shipping',
            resultSelector: '#estimate-result',
            estimateSelector: '#estimate-actions',
            estimateDateSelector: '.estimate-date',
            regionSelector: '[name=region]',
            citySelector: '[name=city]',
            townSelector: '[name=town]',
            productSelector: '[name=product_id]'
        },

        /**
         * @private
         */
        _create: function () {
            this.modal = $(this.options.modalSelector);
            this.form = this.modal.find(this.options.formSelector);
            this.region = this.form.find(this.options.regionSelector);
            this.city = this.form.find(this.options.citySelector);
            this.town = this.form.find(this.options.townSelector);

            this._initModal();
            this._bind();
        },

        _initModal: function () {
            var self = this;

            var options = {
                title: $t('Estimate Shipping'),
                type: 'popup',
                modalClass: 'modal-estimate-shipping',
                responsive: true,
                buttons: [{
                    text: $t('Estimate'),
                    click: function () {
                        self._estimate();
                    }
                }, {
                    text: $t('Close'),
                    class: 'action-secondary action-dismiss',
                    click: function () {
                        this.closeModal(true);
                        self.form.validation('clearError')
                    }
                }]
            };

            modal(options, $(this.options.modalSelector));
        },

        _bind: function () {
            var handlers = {},
                self = this;

            handlers['click ' + this.options.calculateButton] = this.options.ubigeo ? '_onCalculate' : '_onShowModal';
            handlers['click ' + this.options.recalculateButton] = '_onShowModal';

            this._on(handlers);

            this.region.on('change', function () {
                self._onRegionChange();
            });

            this.city.on('change', function () {
                self._onCityChange();
            });
        },

        _onShowModal: function (e) {
            e.stopPropagation();

            this.modal.modal('openModal').trigger('contentUpdated');
        },

        _onCalculate: function () {
            this._estimate(this.options.ubigeo);
        },

        _estimate: function (ubigeo) {
            var data,
                self = this;

            if (!ubigeo) {
                this.form.validation();

                if (!this.form.validation('isValid')) {
                    return false;
                }

                data = this.form.serialize();
            }

            if (ubigeo) {
                data = {
                    ubigeo: ubigeo,
                    product_id: this.form.find(this.options.productSelector).val()
                };
            }

            $('body').trigger('processStart');

            $.ajax({
                type: 'POST',
                url: url.build('fast-shipping/estimate'),
                dataType: 'json',
                data: data,
                global: false
            }).done(function (response) {
                if (!response.success) {
                    alert(response.message);

                    return false;
                }

                self._updateEstimation(response, !!ubigeo);
            }).always(function () {
                $('body').trigger('processStop');
            });
        },

        _onRegionChange: function () {
            var self = this,
                regionValue = this.region.val();

            this.city.find('option:not(:first)').remove();
            this.town.find('option:not(:first)').remove();

            if (!regionValue) {
                return false;
            }

            $('body').trigger('processStart');

            var payload = {
                'region_id': regionValue
            };

            $.ajax({
                url: url.build('zipcode/index/getcity'),
                dataType: 'json',
                data: payload,
                global: false
            }).done(function (response) {
                response = JSON.parse(response);

                $.each(response, function (index, item) {
                    self.city.append(new Option(item.label, item.value));
                });
            }).always(function () {
                $('body').trigger('processStop');
            });
        },

        _onCityChange: function () {
            var self = this,
                regionValue = this.region.val(),
                cityValue = this.city.val()
            ;

            this.town.find('option:not(:first)').remove();

            if (!cityValue) {
                return false;
            }

            $('body').trigger('processStart');

            var payload = {
                'region_id': regionValue,
                'city': cityValue
            };

            $.ajax({
                type: 'POST',
                url: url.build('zipcode/index/gettown'),
                dataType: 'json',
                data: payload,
                global: false
            }).done(function (response) {
                response = JSON.parse(response);

                $.each(response, function (index, item) {
                    self.town.append(new Option(item.label, item.postcode));
                });
            }).always(function () {
                $('body').trigger('processStop');
            });
        },

        _updateEstimation: function (result, closeModal) {
            var resultElement = this.element.find(this.options.resultSelector);
            resultElement.find(this.options.estimateDateSelector).text(result.dateFormat);

            this.element.find(this.options.estimateSelector).hide();
            resultElement.show();

            if (this.modal.modal('option', 'isOpen')) {
                this.modal.modal('closeModal');
            }
        },
    });

    return $.wolfsellers.fastShipping;
});
