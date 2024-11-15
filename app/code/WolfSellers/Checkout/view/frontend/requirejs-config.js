var config = {
    map:{
      '*':{
          'Magento_Checkout/js/model/step-navigator':'WolfSellers_Checkout/js/model/step-navigator',
          'Magento_Checkout/js/view/form/element/email':'WolfSellers_Checkout/js/view/form/element/email',
          'Magento_Checkout/js/model/shipping-save-processor/default':'WolfSellers_Checkout/js/model/shipping-save-processor/default',
          'Magento_Checkout/js/model/shipping-save-processor/payload-extender':'WolfSellers_Checkout/js/model/shipping-save-processor/payload-extender'
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
            },
            'Magento_InventoryInStorePickupFrontend/js/view/store-selector': {
                'WolfSellers_Checkout/js/view/store-selector-mixin': true
            },
            'Magento_InventoryInStorePickupFrontend/js/view/store-pickup':{
                'WolfSellers_Checkout/js/view/store-pickup-mixin': true
            },
            'Magento_Checkout/js/view/payment/list': {
                'WolfSellers_Checkout/js/view/payment/list-mixin': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'WolfSellers_Checkout/js/view/summary/abstract-total-mixins': true
            },
            "Magento_Tax/js/view/checkout/summary/shipping": {
                "WolfSellers_Checkout/js/view/checkout/summary/shipping-mixin" : true
            },
            'Magento_Ui/js/lib/validation/rules': {
                'WolfSellers_Checkout/js/validation/validator-mixin': true
            }
        }
    }
};
