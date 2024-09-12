define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/form/element/select',
    'Amasty_CheckoutCore/js/model/shipping-rate-service-override',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'uiRegistry',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, quote, select, rateService, rateRegistry, registry, fullScreenLoader) {
    'use strict';

    return select.extend({
        defaults: {
            colonies : [],
        },
        
        initialize: function () {
            this._super();
            var self = this;

            $(document).on('change', '[name="custom_attributes[city]"]', function () {
                var selectedCity = $(this).val();
                console.log('Ciudad seleccionada:', selectedCity);
                 var regionId = $(this).parent().parent().parent().find('select[name="region_id"]').val();
                self.filterCities(selectedCity,regionId);
                    fullScreenLoader.startLoader();
            });


            $(document).on('change', '[name="custom_attributes[colony]"]', function () {
                var selectedColony = $(this).val();
               self.recalculateShippingRates(selectedColony, quote);
            });
        },

        filterCities: function (cityId, regionId) {
            var self = this;
            $.ajax({
                url: '/zipcode/index/gettown', 
                data: {region_id: regionId, city: cityId },
                success: function (data) {
                    console.log(data);
                    this.colonies = JSON.parse(data);
                    self.setOptions(this.colonies);
                    fullScreenLoader.stopLoader();
                }
            });
        },
        recalculateShippingRates: function (selectedColony, quote) {
            var optSelected;
            var zipcode;
            if(_.isUndefined(selectedColony) && _.isEmpty(selectedColony)){
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

            var shipping = quote.shippingAddress();
            if (shipping && shipping.regionId && shipping.countryId) {
                rateRegistry.set(shipping.getKey(), null);
                rateRegistry.set(shipping.getCacheKey(), null);
                quote.shippingAddress(shipping);
                rateService.updateRates(quote.shippingAddress());
                console.log('Shipments update');
            }else{
                console.log('no es posible actualizar shipments');
            }
            
        }
    });
});
