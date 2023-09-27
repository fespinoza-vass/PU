var config = {
    map:{
      '*':{
          'Magento_Checkout/js/model/step-navigator':'WolfSellers_Checkout/js/model/step-navigator',
          'Magento_Checkout/js/view/form/element/email':'WolfSellers_Checkout/js/view/form/element/email'
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
            },
            'Amasty_Label/js/label': {
                'WolfSellers_Checkout/js/label': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'WolfSellers_Checkout/js/view/form/element/abstract': true
            },
            'Magento_Checkout/js/view/progress-bar': {
                'WolfSellers_Checkout/js/view/progress-bar-mixin': true
            }
        }
    }
};
