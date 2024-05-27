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
               if(!_.isUndefined(shipping)){
                   if(shipping.isRegularMethodConfigured() && shipping.isRegularShipping()){
                        shipping.setRegularShipping();
                   }
                   if(shipping.isUrbanoMethodConfigured() && shipping.isUrbanoShipping()){
                        shipping.setUrbanoShipping();
                   }
                   if(shipping.isFastMethodConfigured() && shipping.isFastShipping()){
                        shipping.setFastShipping();
                   }
               }
           }
           this._super(shippingMethod);
       }
   };

   return function (storePickupTarget) {
       return storePickupTarget.extend(storePickUpMixin);
   }

});
