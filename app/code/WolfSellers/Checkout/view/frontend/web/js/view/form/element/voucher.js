define([
    'ko',
    'underscore',
    'jquery',
    'Magento_Ui/js/form/element/select',
    'mage/url'
], function (
    ko,
    _,
    $,
    Select,
    url
) {
   'use strict'
   return Select.extend({
       initialize:function () {
           this._super();
           var regionId = 2935;
           var city = "LIMA";
           var payload = {
               'region_id': regionId,
               'city': city
           };
            var self = this;
           $.ajax({
               url: url.build('zipcode/index/gettown'),
               dataType: 'json',
               data: payload,
               global: false
           }).done(function (response) {
               response = JSON.parse(response);
               response = _.map(response, function (item) {
                   if(!_.isUndefined(item)){
                       if(item.label.length >= 3){
                           item.label = item.label.charAt(0).toUpperCase() + item.label.slice(1).toLowerCase();
                       }
                       return item;
                   }
                   return item;
               });
               self.options(response);
           }).always(function () {
               $('body').trigger('processStop');
           });
           return this;
       },
       /**
        * @param value
        *
        * @returns {*}
        */
       onUpdate: function (value) {
           if(_.isEmpty(value)){
               return;
           }
           return this._super();
       }
   });
});
