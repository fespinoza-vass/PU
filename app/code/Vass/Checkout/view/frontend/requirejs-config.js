var config = {
    map: {
        '*': {
            'general-validation': 'Vass_Checkout/js/general-validation',
            'validation-documento': 'Vass_Checkout/js/validation-documento',
            'Magento_SalesRule/template/payment/discount.html': 'Vass_Checkout/template/payment/discount.html',
            'popups': 'Vass_Checkout/js/popups',
            'texto-checkbox': 'Vass_Checkout/js/texto-checkbox',
            'Magento_Customer/js/action/login':'Vass_Checkout/js/action/login'
        }
    },
    paths: {
        'general-validation': 'Vass_Checkout/js/general-validation',
        'validation-documento': 'Vass_Checkout/js/validation-documento',
        'popups': 'Vass_Checkout/js/popups',
        'texto-checkbox': 'Vass_Checkout/js/texto-checkbox'
    },
    shim: {
        'general-validation': {
            deps: ['jquery'],
            exports: 'general-validation'
        },
        'validation-documento': {
            deps: ['jquery'],
            exports: 'validation-documento'
        },
        'popups': {
            deps: ['jquery'],
            exports: 'popups'
        },
        'texto-checkbox': {
            deps: ['jquery'],
            exports: 'popups'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Vass_Checkout/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'Vass_Checkout/js/view/payment/default-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'Vass_Checkout/js/view/shipping-information/address-renderer/default-mixin': true
            },
            'Magento_Checkout/js/view/billing-address/list': {
                'Vass_Checkout/js/view/billing-address/list-mixin': true
            },
            'Amasty_CheckoutCore/js/view/payment/method-renderer/default-mixin': {
                'Vass_Checkout/js/view/payment/method-renderer/default-mixin': true
            }
        }
    }
};
