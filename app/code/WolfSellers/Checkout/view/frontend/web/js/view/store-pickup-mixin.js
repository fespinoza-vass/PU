define([
    'ko',
    'uiRegistry'
], function (
    ko,
    registry
) {
   'use strict';

   var storePickUpMixin = {
       defaults : {
           deliveryMethodSelectorTemplate: 'WolfSellers_Checkout/delivery-method-selector'
       },
       /**
        * disable preselection
        * @returns {boolean}
        */
       preselectLocation: function (){
           if (!this.isStorePickupSelected()) {
               return;
           }
           return true;
       },
       /**
        * validate select shipping method
        * @param shippingMethod
        */
       selectShippingMethod: function (shippingMethod) {
           if(!shippingMethod.carrier_code.includes('instore')){
               var shipping = registry.get("checkout.steps.shipping-step.shippingAddress");
               if(shipping.isUrbanoMethodConfigured() && !shipping.isRegularMethodConfigured()){
                   shipping.setUrbanoShipping();
               }else{
                   shipping.setRegularShipping();
               }
           }
           this._super(shippingMethod);
       }
   };

   return function (storePickupTarget) {
       return storePickupTarget.extend(storePickUpMixin);
   }

});
