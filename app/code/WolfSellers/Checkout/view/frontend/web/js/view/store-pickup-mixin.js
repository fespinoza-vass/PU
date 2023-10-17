define([
    'ko'
], function (
    ko
) {
   'use strict';

   var storePickUpMixin = {
       defaults : {
           deliveryMethodSelectorTemplate: 'WolfSellers_Checkout/delivery-method-selector'
       }
   };

   return function (storePickupTarget) {
       return storePickupTarget.extend(storePickUpMixin);
   }

});
