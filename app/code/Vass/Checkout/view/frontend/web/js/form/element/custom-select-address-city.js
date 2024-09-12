define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'ko',
    'Magento_Ui/js/form/element/select'
], function ($, quote, ko, select) {
    'use strict';

    return select.extend({
        initialize: function () {
            this._super();
            var self = this;

            $(document).on('change', '[name="region_id"]', function () {
                var selectedRegion = $(this).val();
                console.log('Region seleccionada:', selectedRegion);
                self.filterCities(selectedRegion);
            });
            
        },

        filterCities: function (regionId) {
            var self = this;
            $.ajax({
                url: '/zipcode/index/getcity',  
                data: { region_id: regionId },
                success: function (data) {
                    console.log(self);
                    self.setOptions(JSON.parse(data));
                }
            });
        }
    });
});
