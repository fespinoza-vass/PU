define([
    'ko'
], function (
    ko
) {
   'use strict';

   var storePickUpMixin = {
       default : {
           template: 'WolfSellers_Checkout/store-pickup'
       }
   };

   return function (storePickupTarget) {
       return storePickupTarget.extend(storePickUpMixin);
   }

});
