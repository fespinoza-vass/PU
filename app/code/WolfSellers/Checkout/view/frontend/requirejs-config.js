var config = {
    map:{
      '*':{
          'Magento_Checkout/js/model/step-navigator':'WolfSellers_Checkout/js/model/step-navigator'
      }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/shipping': {
                'WolfSellers_Checkout/js/view/summary/shipping-mixin': true
            },
            'Magento_Checkout/js/view/payment' : {
                'WolfSellers_Checkout/js/view/payment-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'WolfSellers_Checkout/js/view/shipping-mixin': true
            }
        }
    }
};
