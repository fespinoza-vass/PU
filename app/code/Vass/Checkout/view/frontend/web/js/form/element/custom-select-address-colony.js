define([
    'ko',
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/form/element/select',
    'Amasty_CheckoutCore/js/model/shipping-rate-service-override',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/translate'
], function (ko, $, registry, quote, select, rateService, rateRegistry, fullScreenLoader) {
    'use strict';

    return select.extend({
        isLoaded: ko.observable(false),
        isLoadedColony: ko.observable(false),

        /**
         * Initializes component.
         */
        initialize: function () {
            this._super();
            let self = this;

            $(document).on('change', '[name="custom_attributes[city]"]', function () {
                let id = $(this).attr('id');
                let shippingContainer = $('.checkout-shipping-address');
                if (shippingContainer.find(`#${id}`) && shippingContainer.is(":visible")) {
                    if (
                        shippingContainer.find('select[name="region_id"]').is(':visible')
                        && shippingContainer.find('select[name="custom_attributes[city]"]').is(':visible')
                        && shippingContainer.find('select[name="custom_attributes[colony]"]').is(':visible')
                    ) {
                        let regionId = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id');
                        let regionIdValue = regionId.value();
                        let city = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.city');
                        let cityValue = city.value();

                        if (cityValue && regionIdValue) {
                            fullScreenLoader.startLoader();
                            let address = quote.shippingAddress();
                            address.city = cityValue;
                            quote.shippingAddress(address);
                            self.filterCities(cityValue, regionIdValue);
                        }
                    }
                }
            });

            let intervalFunction = setInterval(function(){
                $('body').trigger('processStart');
                let loader = $('.loading-mask').length;
                if (loader === 1) {
                    $('body').trigger('processStop');
                    $('body > .loading-mask').remove();
                    $(document).off('ajaxComplete');
                    $(".billing-address-same-as-shipping").click();
                    clearInterval(intervalFunction);
                }
            }, 2000);

            $(document).on('change', '[name="custom_attributes[colony]"]', function () {
                if (self.isLoadedColony()) {
                    let selectedColony = $(this).val();
                    self.recalculateShippingRates(selectedColony, quote);
                } else {
                    self.isLoadedColony(true);
                }
            });

            $(document).ajaxComplete(function () {
                $('#opc-new-shipping-address').siblings('.actions-toolbar').find('.action-update').click()
            });
        },

        /**
         * Filters colony by city.
         *
         * @param {string} cityId
         * @param {string} regionId
         */
        filterCities: function (cityId, regionId) {
            let self = this;
            $.ajax({
                url: '/zipcode/index/gettown',
                data: {region_id: regionId, city: cityId},
                success: function (data) {
                    let colonies = JSON.parse(data);
                    self.setOptions(colonies);
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Recalculates shipping rates.
         *
         * @param selectedColony
         */
        recalculateShippingRates: function (selectedColony) {
            let self = this;
            let optSelected;
            let zipcode;
            if (_.isUndefined(selectedColony) && _.isEmpty(selectedColony)) {
                return;
            }
            if (typeof this.getOption !== 'function') {
                return;
            }

            if (selectedColony && (optSelected = this.getOption(selectedColony))) {
                zipcode = optSelected.postcode;
            }

            registry.get(this.parentName + '.' + 'postcode', function (postcodeField) {
                postcodeField.value(zipcode);
            }.bind(this));

            let shipping = quote.shippingAddress();
            if (shipping && shipping.regionId && shipping.countryId) {
                rateRegistry.set(shipping.getKey(), null);
                rateRegistry.set(shipping.getCacheKey(), null);
                quote.shippingAddress(shipping);
                rateService.updateRates(quote.shippingAddress());
            }
        }
    });
});
