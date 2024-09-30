define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'ko',
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, quote, ko, select, registry, fullScreenLoader) {
    'use strict';

    return select.extend({
        initialize: function () {
            this._super();
            var self = this;

            $(document).on('change', '[name="region_id"]', function () {
                var selectedRegion = $(this).val();
                if(selectedRegion.length > 0){
                    console.log('Region seleccionada:', selectedRegion);
                    fullScreenLoader.startLoader();
                    self.filterCities(selectedRegion);
                }else{
                    var selectComponent = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id');
                    selectComponent.value('');
                }
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
                    fullScreenLoader.stopLoader();
                }
            });
        }
    });
});
