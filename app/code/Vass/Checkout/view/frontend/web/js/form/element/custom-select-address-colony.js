define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/form/element/select',
    'Amasty_CheckoutCore/js/model/shipping-rate-service-override',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'uiRegistry'
], function ($, quote, select, rateService, rateRegistry, registry ) {
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
            });


            $(document).on('change', '[name="custom_attributes[colony]"]', function () {
                var selectedColony = $(this).val();
                var address = quote.shippingAddress();
                address.postcode = selectedColony;
                quote.shippingAddress(address);
                $('input[name="postcode"]').val(selectedColony);
               self.recalculateShippingRates(selectedColony);
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
                }
            });
        },
        recalculateShippingRates: function (selectedColony) {
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

            // if (address && address.regionId && address.countryId) {
            //     rateRegistry.set(address.getKey(), null);
            //     rateRegistry.set(address.getCacheKey(), null);
            //     quote.shippingAddress(address);
            //     rateService.updateRates(quote.shippingAddress());
            //     console.log('Shipments update');
            // }else{
            //     console.log('no es posible actualizar shipments');
            // }
            
        }
    });
});
